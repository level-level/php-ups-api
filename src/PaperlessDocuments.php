<?php

namespace Ups;

use DOMDocument;
use Exception;
use Psr\Log\LoggerInterface;
use SimpleXMLElement;
use Ups\Entity\Address;
use Ups\Entity\Shipper;
use Ups\Entity\UserCreatedForm;

/**
 * Paperless Documents API Wrapper to use the PaperlessDocuments endpoints.
 */
class PaperlessDocuments extends Ups
{
    const ENDPOINT = '/PaperlessDocumentAPI';

    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var ResponseInterface
     *
     * @todo make private
     */
    public $response;

    /**
     * @var Shipper
     */
    private $shipper;

	/**
     * @var UserCreatedForm
     */
    private $userCreatedForm;

    /**
     * @param string|null $accessKey UPS License Access Key
     * @param string|null $userId UPS User ID
     * @param string|null $password UPS User Password
     * @param bool $useIntegration Determine if we should use production or CIE URLs.
     * @param RequestInterface|null $request
     * @param LoggerInterface|null $logger PSR3 compatible logger (optional)
     */
    public function __construct(
        $accessKey = null,
        $userId = null,
        $password = null,
        $useIntegration = false,
        RequestInterface $request = null,
        LoggerInterface $logger = null
    ) {
        if (null !== $request) {
            $this->setRequest($request);
        }
        parent::__construct($accessKey, $userId, $password, $useIntegration, $logger);
    }

    /**
     * Upload a userCreatedForm to the Paperless Document API
     *
     * @param Shipper $shipper
     *
     * @throws Exception
     *
     * @return array
     */
    public function upload(UserCreatedForm $userCreatedForm, Shipper $shipper)
    {
		$this->userCreatedForm = $userCreatedForm;
        $this->shipper = $shipper;

        $access = $this->createAccess();
        $request = $this->createUploadRequest();

        $this->response = $this->getRequest()->request($access, $request, $this->compileEndpointUrl(self::ENDPOINT));
        $response = $this->response->getResponse();

        if (null === $response) {
            throw new Exception('Failure (0): Unknown error', 0);
        }

        if ($response instanceof SimpleXMLElement && $response->Response->ResponseStatus->Code == 0) {

			$alerts = array();
			if ( isset( $response->Response->Alert ) ) {
				$alerts = $response->Response->Alert;
				if ( ! is_array( $response->Response->Alert ) ) {
					$alerts = array( $response->Response->Alert );
				}
			}
			$alertsJson = json_encode( $alerts );

            throw new Exception(
                "Failure: {$response->Response->ResponseStatus->Description} Alerts: {$alertsJson}",
                (int)$response->Response->ResponseStatus->Code
            );
        }

        return $this->formatResponse($response);
    }

    /**
     * Create the PD Upload Request request.
     *
     * @return string
     */
    private function createUploadRequest()
    {
        $xml = new DOMDocument();
        $xml->formatOutput = true;

        $pdRequest = $xml->appendChild($xml->createElement('UploadRequest'));
        $pdRequest->setAttribute('xml:lang', 'en-US');

        $request = $pdRequest->appendChild($xml->createElement('Request'));

        $node = $xml->importNode($this->createTransactionNode(), true);
        $request->appendChild($node);

		if ( $this->shipper ) {
			$request->appendChild($xml->createElement('ShipperNumber', $this->shipper->getShipperNumber()));
		}

		if ( $this->userCreatedForm ) {
			$request->appendChild($xml->importNode($this->userCreatedForm->toNode(), true));
		}

        return $xml->saveXML();
    }

    /**
     * Format the response.
     *
     * @param SimpleXMLElement $response
     *
     * @return array
     */
    private function formatResponse(SimpleXMLElement $response)
    {
        $result = $this->convertXmlObject($response);

        if (isset( $result->Response->Alert ) && !is_array($result->Response->Alert)) {
            $result->Response->Alert = [$result->Response->Alert];
        }

        return $result->Response;
    }

    /**
     * @return RequestInterface
     */
    public function getRequest()
    {
        if (null === $this->request) {
            $this->request = new Request($this->logger);
        }

        return $this->request;
    }

    /**
     * @param RequestInterface $request
     *
     * @return $this
     */
    public function setRequest(RequestInterface $request)
    {
        $this->request = $request;

        return $this;
    }

    /**
     * @return ResponseInterface
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * @param ResponseInterface $response
     *
     * @return $this
     */
    public function setResponse(ResponseInterface $response)
    {
        $this->response = $response;

        return $this;
    }
}
