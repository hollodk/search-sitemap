<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class SearchController extends AbstractController
{
    /**
     * @Route("")
     */
    public function index(Request $request)
    {
        $sitemap = file_get_contents($_ENV['SITEMAP_URL']);
        $query = $this->clean($request->get($_ENV['QUERY_PARAMETER']));

        $xml = new \SimpleXMLElement($sitemap);
        $res = [];
        $last = null;

        foreach ($xml->url as $url) {
            $search = $this->clean($url->loc);

            if (preg_match("/".$query."/", $search)) {
                $last = $url->loc;

                $res[] = $last;
            }
        }

        if ($last) {
            return $this->redirect($last);
        } else {
            return $this->redirect($request->server->get('HTTP_REFERER'));
        }
    }

    /**
     * @Route("/r")
     */
    public function r(Request $request)
    {
        $url = 'https://mammoth.fly-mailers.space/funnel?h='.$request->get('h');

        return $this->redirect($url);
    }

    private function clean($input)
    {
        $input = preg_replace("/\-/", "", $input);
        $input = preg_replace("/http:\/\//", "", $input);
        $input = preg_replace("/https:\/\//", "", $input);
        $input = preg_replace("/\//", "", $input);

        return $input;
    }
}
