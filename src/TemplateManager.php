<?php

class TemplateManager
{
    public function getTemplateComputed(Template $tpl, array $data): Template
    {
        $tpl->subject = $this->computeText($tpl->subject, $data);
        $tpl->content = $this->computeText($tpl->content, $data);

        return $replaced;
    }

    private function computeText(string $text, array $data): string
    {
        $APPLICATION_CONTEXT = ApplicationContext::getInstance();

        $quote = (isset($data['quote']) and $data['quote'] instanceof Quote) ? $data['quote'] : null;

        if ($quote) {
            $_quoteFromRepository = QuoteRepository::getInstance()->getById($quote->id);
            $usefulObject = SiteRepository::getInstance()->getById($quote->siteId);
            $destinationOfQuote = DestinationRepository::getInstance()->getById($quote->destinationId);

            if(strpos($text, '[quote:destination_link]') !== false) {
                $destination = DestinationRepository::getInstance()->getById($quote->destinationId);
            }

            if (strpos($text, '[quote:summary_html]') !== false) {
                $text = str_replace(
                    '[quote:summary_html]',
                    Quote::renderHtml($_quoteFromRepository),
                    $text
                );
            }
            if (strpos($text, '[quote:summary]') !== false) {
                $text = str_replace(
                    '[quote:summary]',
                    Quote::renderText($_quoteFromRepository),
                    $text
                );
            }

            if (strpos($text, '[quote:destination_name]') !== false) {
                $text = str_replace(
                    '[quote:destination_name]',
                    $destinationOfQuote->countryName,
                    $text
                );
            }
        }

        $destinationText = (isset($destination)) ? $usefulObject->url . '/' . $destination->countryName . '/quote/' . $_quoteFromRepository->id : '';
        $text = str_replace(
            '[quote:destination_link]',
            $destinationText,
            $text
        );

        /*
         * USER
         * [user:*]
         */
        $_user  = (isset($data['user']) and ($data['user']  instanceof User)) ? $data['user'] : $APPLICATION_CONTEXT->getCurrentUser();
        if($_user) {
            if (strpos($text, '[user:first_name]') !== false) {
                $text = str_replace('[user:first_name]'       , ucfirst(mb_strtolower($_user->firstname)), $text);
            }
        }

        return $text;
    }
}
