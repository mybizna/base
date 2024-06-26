<?php

namespace Modules\Base\Classes;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

/**
 * AppMailer class
 *
 * This class is used to send emails
 *
 * @package Modules\Base\Classes
 */
class AppMailer extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Data
     *
     * @var array
     */
    public $data;

    /**
     * Subject
     *
     * @var string
     */
    public $subject;
    /**
     * From email
     *
     * @var string
     */
    public $from_email;
    /**
     * From name
     *
     * @var string
     */
    public $from_name;
    /**
     * Template
     *
     * @var string
     */
    public $template;
    /**
     * Message
     *
     * @var array
     */
    public $message;
    /**
     * Attachments
     *
     * @var array
     */
    public $attachments;
    /**
     * Create a new message instance.
     * 
     * @param array $params
     *
     * @return void
     */
    public function __construct(array $params)
    {
        $this->params = $params;
        $this->data = isset($this->params['data']) ? $this->params['data'] : '';
        $this->attachments = isset($this->params['attachments']) ? $this->params['attachments'] : [];
        $this->subject = isset($this->params['subject']) ? $this->params['subject'] : '';
        $this->message = isset($this->params['message']) ? $this->params['message'] : '';
        $this->from_email = isset($this->params['from_email']) ? $this->params['from_email'] : config('core.company_email');
        $this->from_name = isset($this->params['from_name']) ? $this->params['from_name'] : config('core.company_name');
        $this->template = isset($this->params['template']) ? $this->params['template'] : 'base::email.simple';
    }

    /**
     * Build the message.
     * 
     *
     * @return $this
     */
    public function build()
    {
        $data = $this->params;

        $data['theme_title'] = $this->subject;
        $data['theme_logo'] = $this->subject;
        $data['body'] = $this->subject;
        $data['contact_email'] = '';
        $data['facebook_link'] = '';
        $data['twitter_link'] = '';
        $data['twitter_link'] = '';
        $data['unsubscribe_url'] = '';

        $email = $this->view($this->template)
            ->from($this->from_email, $this->from_name)
            ->with($data);

        if(!empty($this->attachments)){
            foreach ($this->attachments as $key => $attachment) {
                $email->attach($attachment);
            }
        }

        return $email;

    }
}
