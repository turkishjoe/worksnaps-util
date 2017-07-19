<?php

namespace AppBundle\Controller;

use AppBundle\Model\CalcModel;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use DateTime;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        //TODO:To service
        return new Response(json_encode((new CalcModel())->calc($this->get('umbrella.worksnaps'))));
    }
}


