<?php

namespace Modules\Base\Classes;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AppMailer extends Mailable
{
    use Queueable, SerializesModels;
    public $params;
    public $subject;
    public $from_email;
    public $from_name;
    public $template;
    public $template_type;
    public $data;
    public $body;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(array $params)
    {
        $this->params = $params;
        $this->data = isset($this->params['data']) ? $this->params['data'] : '';
        $this->subject = isset($this->params['subject']) ? $this->params['subject'] : '';
        $this->from_email = isset($this->params['from_email']) ? $this->params['from_email'] : config('core.company_email');
        $this->from_name = isset($this->params['from_name']) ? $this->params['from_name'] :  config('core.company_name');
        $this->template = $this->params['template'] ? $this->params['template'] : '';
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {

        if ($this->template_type == 'view') {
            return $this->view($this->template)
                ->from($this->from_email, $this->from_name)
                ->with('data', $this->data);
        } else {
            return $this->markdown($this->template)
                ->from($this->from_email, $this->from_name)
                ->with('data', $this->data)
                ->subject($this->subject);
        }

    }
}
