<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Laravel\Sanctum\HasApiTokens;
use Stripe\Customer as StripeCustomer;
use Stripe\Stripe;
use Stripe\Exception\ApiErrorException;


class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'email',
        'phone',
        'address',
        'tax_id',
        'notes',
        'stripe_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    /**
     * Crear o recuperar un cliente de Stripe
     *
     * @returns \Stripe\Customer
     * @throws \Stripe\Exception\ApiErrorException
     */

    public function asStripeCustomer()
    {
        Stripe::setApiKey(env('STRIPE_SECRET'));
        if ($this->stripe_id) {
            try {
                return StripeCustomer::retrieve($this->stripe_id);
            } catch (ApiErrorException $e) {
                //Si el cliente fue eliminado en Stripe, creamos uno nuevo

            }
        }

        $customer = StripeCustomer::create([
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'address' => !empty($this->address) ? ['line1' => $this->address] : null,
            'metadata' => [
                'customer_id' => $this->id,
                'tax_id' => $this->tax_id,
            ],
        ]);

        //Guardar el ID de Stripe en el modelo
        $this->stripe_id = $customer->id;
        $this->save();

        return $customer;
    }

    /**
     * Actualizar la informacion del cliente en Stripe
     *
     * @return \Stripe\Customer|null
     * @throws \Stripe\Exception\ApiErrorException
     */
    public function updateStripeCustomer()
    {
        if(!$this->stripe_id){
            return $this->asStripeCustomer();
        }

        Stripe::setApiKey(env('STRIPE_SECRET'));

        try{
            $customer = StripeCustomer::update($this->stripe_id, [
                'name' => $this->name,
                'email' => $this->email,
                'phone' => $this->phone,
                'address' => !empty($this->address) ? ['line1' => $this->address] : null,
                'metadata' => [
                    'customer_id' => $this->id,
                    'tax_id' => $this->tax_id,
                ],
            ]);

            return $customer;
        }catch(ApiErrorException $e)
        {
            return $this->asStripeCustomer();
        }
    }

    /**
     * Eliminar el cliente de Stripe
     *
     * @return bool
     */
    public function deleteStripeCustomer()
    {
        if(!$this->stripe_id){
            return false;
        }

        //Configurar la clave API de Stripe
        Stripe::setApiKey(env('STRIPE_SECRET'));

        try{
            $customer = StripeCustomer::retrieve($this->stripe_id);
            $customer->delete();

            //Limpiar el stripe_id en nuestro modelo
            $this->stripe_id = null;
            $this->save();

            return true;
        }catch(APIErrorException $e){
            return false;
        }
    }

    /**
     * Crear o actualizar un método de pago para este cliente
     *
     * @param string $paymentMethodId
     * @return \Stripe\PaymentMethod
     */
    public function updatePaymentMethod(string $paymentMethodId)
    {
        Stripe::setApiKey(env('STRIPE_SECRET'));

        $customer = $this->asStripeCustomer();

        //Adjustar el método de pago al cliente

        $paymentMethod = \Stripe\PaymentMethod::retrieve($paymentMethodId);
        $paymentMethod->attach(['customer' => $customer->id]);

        //Establecer como predeterminado
        $customer->invoice_settings = ['default_payment_method' => $paymentMethodId];
        $customer->save();

        return $paymentMethod;
    }

    /**
     * Obtener todos los métodos de pago del cliente
     *
     * @return array
     */
    public function paymentMethods()
    {
        if(!$this->stripe_id){
            return [];
        }

        Stripe::setApiKey(env('STRIPE_SECRET'));

        try{
            return \Stripe\PaymentMethod::all([
               'customer' => $this->stripe_id,
               'type' => 'card',
            ])->data;
        }catch (ApiErrorException $e)
        {
            return [];
        }
    }
}
