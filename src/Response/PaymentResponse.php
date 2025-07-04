<?php
/**
 * Laravel Paytr
 *
 * @author    Furkan Meclis
 * @copyright 2024 Furkan Meclis
 * @license   MIT
 * @link      https://github.com/furkanmeclis/laravel-paytr
 */

namespace FurkanMeclis\Paytr\Response;

class PaymentResponse
{
    /**
     * @var array
     */
    private array $content;

    /**
     * @var string
     */
    private string $html;

    /**
     * @var bool
     */
    private bool $isSuccess;

    /**
     * @var bool
     */
    private bool $isHtml;

    /**
     * @return array
     */
    public function getContent(): array
    {
        return $this->content;
    }

    /**
     * @param array $content
     * @return PaymentResponse
     */
    public function setContent(array $content): PaymentResponse
    {
        $this->content = $content;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getHtml(): ?string
    {
        if ($this->isSuccess() && !$this->isHtml() && $this->getToken()) {
            $iframeHtml = '<iframe src="https://www.paytr.com/odeme/guvenli/' . $this->getToken() . '" id="paytriframe" frameborder="0" scrolling="no" style="width: 100%;"></iframe>';
            $resizerScript = '<script src="https://www.paytr.com/js/iframeResizer.min.js"></script>';
            $initScript = '<script>iFrameResize({},\'#paytriframe\');</script>';

            return $resizerScript . $iframeHtml . $initScript;
        }

        if (isset($this->html)) {
            return $this->html;
        }

        return '';
    }

    /**
     * @param string $html
     * @return PaymentResponse
     */
    public function setHtml(string $html): PaymentResponse
    {
        $this->html = $html;
        return $this;
    }

    /**
     * @return bool
     */
    public function isSuccess(): bool
    {
        return $this->isSuccess;
    }

    /**
     * @param bool $isSuccess
     * @return PaymentResponse
     */
    public function setIsSuccess(bool $isSuccess): PaymentResponse
    {
        $this->isSuccess = $isSuccess;
        return $this;
    }

    /**
     * @return bool
     */
    public function isHtml(): bool
    {
        return $this->isHtml;
    }

    /**
     * @param bool $isHtml
     * @return PaymentResponse
     */
    public function setIsHtml(bool $isHtml): PaymentResponse
    {
        $this->isHtml = $isHtml;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getMessage(): ?string
    {
        $content = $this->getContent();

        return isset($content['reason']) ? $content['reason'] : null;
    }

    /**
     * @return string|null
     */
    public function getToken(): ?string
    {
        $content = $this->getContent();

        return isset($content['token']) ? $content['token'] : null;
    }
}
