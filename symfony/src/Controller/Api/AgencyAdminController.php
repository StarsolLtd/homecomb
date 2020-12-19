<?php

namespace App\Controller\Api;

use App\Controller\AppController;
use App\Model\Agency\CreateAgencyInput;
use App\Repository\AgencyRepository;
use App\Service\AgencyService;
use App\Service\GoogleReCaptchaService;
use Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class AgencyAdminController extends AppController
{
    private AgencyService $agencyService;
    private GoogleReCaptchaService $googleReCaptchaService;
    private AgencyRepository $agencyRepository;
    private SerializerInterface $serializer;

    public function __construct(
        AgencyService $agencyService,
        GoogleReCaptchaService $googleReCaptchaService,
        AgencyRepository $agencyRepository,
        SerializerInterface $serializer
    ) {
        $this->agencyService = $agencyService;
        $this->googleReCaptchaService = $googleReCaptchaService;
        $this->agencyRepository = $agencyRepository;
        $this->serializer = $serializer;
    }

    /**
     * @Route (
     *     "/api/verified/agency",
     *     name="create-agency",
     *     methods={"POST"}
     * )
     */
    public function createAgency(Request $request): JsonResponse
    {
        try {
            $this->denyAccessUnlessGranted('ROLE_USER');
        } catch (Exception $e) {
            return new JsonResponse(
                [
                    'success' => false,
                ],
                Response::HTTP_FORBIDDEN
            );
        }

        /** @var CreateAgencyInput $input */
        $input = $this->serializer->deserialize($request->getContent(), CreateAgencyInput::class, 'json');

        if (!$this->verifyReCaptcha($input->getGoogleReCaptchaToken(), $request)) {
            $this->addFlash('error', 'Sorry, we were unable to process your agency creation.');

            return new JsonResponse([], Response::HTTP_BAD_REQUEST);
        }

        $output = $this->agencyService->createAgency($input, $this->getUserInterface());

        $this->addFlash(
            'notice',
            'Your agency was received successfully and will be reviewed by our moderation team before being '
            .'published shortly. You can now add branches, upload a logo etc.'
        );

        return new JsonResponse(
            [
                'success' => $output->isSuccess(),
            ],
            Response::HTTP_CREATED
        );
    }

    private function verifyReCaptcha(?string $token, Request $request): bool
    {
        return $this->googleReCaptchaService->verify($token, $request->getClientIp(), $request->getHost());
    }
}
