<?php

class TemplateManager
{
    public function getTemplateComputed(Template $tpl, array $data): Template
    {
        $tpl->subject = $this->computeText($tpl->subject, $data);
        $tpl->content = $this->computeText($tpl->content, $data);

        return $tpl;
    }

    private function computeText(string $text, array $data): string
    {
        /*
         * Quote
         * [quote:*]
         */
        $quote = $data['quote'] ?? null;
        if ($quote instanceof Quote) {
            $site = SiteRepository::getInstance()->getById($quote->siteId);
            $destination = DestinationRepository::getInstance()->getById($quote->destinationId);

            $text = str_replace(
                [
                    '[quote:summary_html]',
                    '[quote:summary]',
                    '[quote:destination_name]',
                    '[quote:destination_link]',
                ],
                [
                    Quote::renderHtml($quote),
                    Quote::renderText($quote),
                    $destination->countryName,
                    $site->url . '/' . $destination->countryName . '/quote/' . $quote->id,
                ],
                $text
            );
        }

        /*
         * USER
         * [user:*]
         */
        $user = $data['user'] ?? ApplicationContext::getInstance()->getCurrentUser();
        if($user instanceof User) {
            $text = str_replace(
                '[user:first_name]' ,
                ucfirst(mb_strtolower($user->firstname)),
                $text
            );
        }

        return $text;
    }
}
