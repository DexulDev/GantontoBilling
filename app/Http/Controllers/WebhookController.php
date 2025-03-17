<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Stripe\Webhook;
use Stripe\Exception\SignatureVerificationException;
use Illuminate\Support\Facades\Log;
use App\Events\StripeWebhookReceived;

class WebhookController extends Controller
{
    /**
     * Maneja los webhooks entrantes de Stripe.
     */
    public function handleStripeWebhook(Request $request)
    {
        $payload = $request->getContent();
        $sig_header = $request->header('Stripe-Signature');
        $endpoint_secret = env('STRIPE_WEBHOOK_SECRET');

        try {
            $event = Webhook::constructEvent(
                $payload, $sig_header, $endpoint_secret
            );

        event(new StripeWebhookReceived($event->data->object->toArray(), $event->type));

        } catch (SignatureVerificationException $e) {
            Log::error('Error de verificación de firma de Stripe: ' . $e->getMessage());
            return response()->json(['error' => 'Firma inválida'], 400);
        } catch (\Exception $e) {
            Log::error('Error en webhook de Stripe: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 400);
        }

        // Registrar el evento recibido
        Log::info('Webhook de Stripe recibido: ' . $event->type);

        // Manejar diferentes tipos de eventos
        switch ($event->type) {
            case 'invoice.payment_succeeded':
                return $this->handlePaymentSucceeded($event);

            case 'invoice.payment_failed':
                return $this->handlePaymentFailed($event);

            case 'customer.subscription.created':
            case 'customer.subscription.updated':
                return $this->handleSubscriptionChange($event);

            case 'customer.subscription.deleted':
                return $this->handleSubscriptionDeleted($event);

            case 'customer.updated':
                return $this->handleCustomerUpdated($event);

            case 'payment_method.attached':
                return $this->handlePaymentMethodAttached($event);

            default:
                // Para otros eventos, simplemente registramos y devolvemos respuesta exitosa
                Log::info('Evento de Stripe no manejado: ' . $event->type);
                return response()->json(['status' => 'received', 'type' => $event->type]);
        }
    }

    /**
     * Maneja el evento de pago exitoso de factura.
     */
    protected function handlePaymentSucceeded($event)
    {
        $stripeInvoice = $event->data->object;

        try {
            // Buscar la factura local por el ID de Stripe
            $invoice = Invoice::where('stripe_invoice_id', $stripeInvoice->id)->first();

            if ($invoice) {
                // Actualizar el estado de la factura a "pagada"
                $invoice->status = 'paid';
                $invoice->paid_at = now();
                $invoice->save();

                Log::info("Factura #{$invoice->id} marcada como pagada por webhook de Stripe");

                // Aquí podrías enviar notificaciones, correos, etc.
            } else {
                Log::warning("No se encontró factura local para el ID de Stripe: {$stripeInvoice->id}");
            }

            return response()->json(['status' => 'success']);
        } catch (\Exception $e) {
            Log::error("Error al procesar pago exitoso: " . $e->getMessage());
            return response()->json(['status' => 'error'], 500);
        }
    }

    /**
     * Maneja el evento de pago fallido de factura.
     */
    protected function handlePaymentFailed($event)
    {
        $stripeInvoice = $event->data->object;

        try {
            // Buscar la factura local por el ID de Stripe
            $invoice = Invoice::where('stripe_invoice_id', $stripeInvoice->id)->first();

            if ($invoice) {
                // Actualizar el estado de la factura a "fallida"
                $invoice->status = 'payment_failed';
                $invoice->save();

                Log::info("Factura #{$invoice->id} marcada como fallida por webhook de Stripe");

                // Aquí podrías enviar notificaciones sobre el fallo
            }

            return response()->json(['status' => 'success']);
        } catch (\Exception $e) {
            Log::error("Error al procesar pago fallido: " . $e->getMessage());
            return response()->json(['status' => 'error'], 500);
        }
    }

    /**
     * Maneja eventos de cambio en suscripción.
     */
    protected function handleSubscriptionChange($event)
    {
        $subscription = $event->data->object;

        // Implementa según tus necesidades si manejas suscripciones
        Log::info("Suscripción actualizada: {$subscription->id}");

        return response()->json(['status' => 'success']);
    }

    /**
     * Maneja la eliminación de una suscripción.
     */
    protected function handleSubscriptionDeleted($event)
    {
        $subscription = $event->data->object;

        // Implementa según tus necesidades si manejas suscripciones
        Log::info("Suscripción eliminada: {$subscription->id}");

        return response()->json(['status' => 'success']);
    }

    /**
     * Maneja la actualización de datos del cliente en Stripe.
     */
    protected function handleCustomerUpdated($event)
    {
        $stripeCustomer = $event->data->object;

        try {
            // Buscar el cliente local por stripe_id
            $customer = Customer::where('stripe_id', $stripeCustomer->id)->first();

            if ($customer) {
                // Actualizar información del cliente si es necesario
                // Por ejemplo, actualizar email si ha cambiado en Stripe
                if ($customer->email !== $stripeCustomer->email) {
                    $customer->email = $stripeCustomer->email;
                    $customer->save();

                    Log::info("Datos de cliente #{$customer->id} actualizados desde Stripe");
                }
            }

            return response()->json(['status' => 'success']);
        } catch (\Exception $e) {
            Log::error("Error al actualizar cliente desde Stripe: " . $e->getMessage());
            return response()->json(['status' => 'error'], 500);
        }
    }

    /**
     * Maneja el evento de método de pago adjuntado a un cliente.
     */
    protected function handlePaymentMethodAttached($event)
    {
        $paymentMethod = $event->data->object;

        Log::info("Método de pago adjuntado para cliente: {$paymentMethod->customer}");

        return response()->json(['status' => 'success']);
    }
}
