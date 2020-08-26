<?php

namespace App\Controller;

use App\Entity\OauthAuthorizationCode;
use App\Repository\OauthAuthorizationCodeRepository;
use DateTime;
use http\Exception\InvalidArgumentException;
use Ramsey\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class OauthController extends AbstractController
{
    const GRANT_CLIENT_ACCESS = 'ja';
    const DENY_CLIENT_ACCESS = 'nee';

    private SessionInterface $session;
    private OauthAuthorizationCodeRepository $authorizationCodeRepository;

    public function __construct(SessionInterface $session, OauthAuthorizationCodeRepository $authorizationCodeRepository)
    {
        $this->session = $session;
        $this->authorizationCodeRepository = $authorizationCodeRepository;
    }

    /**
     * @Route("/start-oauth-authorizationserver", name="start-oauth-authorizationserver")
     */
    public function startOauthProcess(Request $request)
    {
        if (
            !empty($request->query->get('response_type')) &&
            !empty($request->query->get('client_id')) &&
            !empty($request->query->get('redirect_uri')) &&
            !empty($request->query->get('scope')) &&
            !empty($request->query->get('state'))
        ) {
            $this->session->set('response_type', $request->query->get('response_type'));
            $this->session->set('client_id', $request->query->get('client_id'));
            $this->session->set('redirect_uri', $request->query->get('redirect_uri'));
            $this->session->set('scope', $request->query->get('scope'));
            $this->session->set('state', $request->query->get('state'));
        }

        $user = $this->getUser();
        if ($user === null) {
            // Save value in session to redirect user back here after login
            $this->session->set('oauth-login-required', true);
            return $this->redirectToRoute('app_login');
        }

        return $this->redirectToRoute('oauth-ask-permission');
    }

    /**
     * @Route("/oauth-ask-permission", name="oauth-ask-permission")
     * @param Request $request
     */
    public function oauthAskPermission()
    {
        $clientId = $this->session->get('client_id');

        return $this->render('oauth/ask.permission.html.twig', [

        ]);
    }

    /**
     * @Route("/oauth-send-user-back", name="oauth-send-user-back")
     */
    public function sendUserBackToClient(Request $request)
    {
        if ($request->query->get('decision') === self::GRANT_CLIENT_ACCESS) {
            $tempCode = $this->createTempCode();
        }

        if ($request->query->get('decision') === self::DENY_CLIENT_ACCESS) {
            $tempCode = 'User denied access';
        }

        // ToDo: gebruik de redirectUri vanaf spam4all tot hier en terug, maar dan meot ik registratie process ook aanpassen deze url te gebruiken
        $redirectUri = $this->session->get('redirect_uri');
        return $this->redirect('http://host.docker.internal:8600/oauth-result?tempcode=' . $tempCode);
    }

    private function createTempCode()
    {
        //return 'this is the tempcode!';
        $tempCode = Uuid::uuid4();
        $dateTime = new DateTime('+2 hour +1min');

        $oAuthAuthorizationCode = new OauthAuthorizationCode();
        $oAuthAuthorizationCode->setClientId($this->session->get('client_id'));
        $oAuthAuthorizationCode->setAuthorizationCode($tempCode);
        $oAuthAuthorizationCode->setExpires($dateTime);
        $oAuthAuthorizationCode->setScope($this->session->get('scope'));

        $this->authorizationCodeRepository->save($oAuthAuthorizationCode);

        return $tempCode;
    }
}