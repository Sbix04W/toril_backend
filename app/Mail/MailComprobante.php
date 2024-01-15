<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class MailComprobante extends Mailable
{
    use Queueable, SerializesModels;
    public $data;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {

        $message = $this->from('xavier12joh@gmail.com', 'TORIL')
        ->subject('Comprobante de Pago')
                ->view('comprobantepago');
        $message->attach(storage_path() . '/app/emitidos/comprobante_' .$this->data->id_recibo.".pdf");
        return $message;

      
    }
}
