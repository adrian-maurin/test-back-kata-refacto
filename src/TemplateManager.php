<?php

class TemplateManager
{
    /**
     * get computed template using input data
     *
     * @param Template $tpl
     * @param array $data input data, may contain Quote and User data
     * @return Template computed template using input $tpl and $data
     * @access public
     */
    public function getTemplateComputed(Template $tpl, array $data): Template
    {
        $tpl->subject = $this->computeText($tpl->subject, $data);
        $tpl->content = $this->computeText($tpl->content, $data);

        return $tpl;
    }



    /**
     * compute some text, replace tags according to input data
     *
     * @param string $text text to compute
     * @param array $data input data to get replacement text from
     * @return string computed text using input $text and $data
     * @access private
     */
    private function computeText(string $text, array $data): string
    {
        /*
         * QUOTE
         * [quote:*]
         */
        $quote = $data['quote'] ?? null;
        if ($quote instanceof Quote) {
            // get site & destination info using respective repositories
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
