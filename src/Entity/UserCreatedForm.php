<?php

namespace Ups\Entity;

use DOMDocument;
use DOMElement;
use Ups\NodeInterface;

class UserCreatedForm implements NodeInterface
{

	public const ALLOWED_FILE_FORMATS = array('bmp', 'doc', 'docx', 'gif', 'jpg', 'pdf', 'png', 'rtf', 'tif', 'txt', 'xls', 'xlsx');

	public const DOCUMENT_TYPE_AUTHORIZATION_FORM = '001';
	public const DOCUMENT_TYPE_COMMERCIAL_INVOICE = '002';
	public const DOCUMENT_TYPE_CERTIFICATE_OF_ORIGIN = '003';
	public const DOCUMENT_TYPE_EXPORT_ACCOMPANYING_DOCUMENT = '004';
	public const DOCUMENT_TYPE_EXPORT_LICENCE = '005';
	public const DOCUMENT_TYPE_IMPORT_PERMIT = '006';
	public const DOCUMENT_TYPE_ONE_TIME_NAFTA = '007';
	public const DOCUMENT_TYPE_OTHER_DOCUMENT = '008';
	public const DOCUMENT_TYPE_POWER_OF_ATTORNEY = '009';
	public const DOCUMENT_TYPE_PACKING_LIST = '010';
	public const DOCUMENT_TYPE_SED_DOCUMENT = '011';
	public const DOCUMENT_TYPE_SHIPPERS_LETTER_OF_INSTRUCTION = '012';
	public const DOCUMENT_TYPE_DECLARATION = '013';


	/**
	 * File name
	 *
	 * @var string $fileName
	 */
	private $fileName;

	/**
	 * File extension
	 *
	 * @var string $fileFormat
	 */
	private $fileFormat;

	/**
	 * Document type
	 *
	 * @var string $documentType
	 */
	private $documentType;

	/**
	 * File
	 *
	 * @var string $file
	 */
	private $file;

    /**
     * @param \stdClass|null $attributes
     */
    public function __construct(\stdClass $attributes = null)
    {
        if (null !== $attributes) {
            if (isset($attributes->FileName)) {
                $this->setFileName($attributes->FileName);
            }
			if (isset($attributes->FileFormat)) {
                $this->setFileFormat($attributes->FileFormat);
            }
			if (isset($attributes->DocumentType)) {
                $this->setDocumentType($attributes->DocumentType);
            }
			if (isset($attributes->File)) {
                $this->setFile($attributes->File);
            }
        }
    }

    /**
     * @param null|DOMDocument $document
     *
     * @return DOMElement
     */
    public function toNode(DOMDocument $document = null)
    {
        if (null === $document) {
            $document = new DOMDocument();
        }

        $node = $document->createElement('UserCreatedForm');
        $node->appendChild($document->createElement('UserCreatedFormFileName', $this->getFileName()));
        $node->appendChild($document->createElement('UserCreatedFormFileFormat', $this->getFileFormat()));
		$node->appendChild($document->createElement('UserCreatedFormDocumentType', $this->getDocumentType()));
        $node->appendChild($document->createElement('UserCreatedFormFile', $this->getFile()));
        return $node;
    }

    /**
     * @return string
     */
    public function getFileName()
    {
        return $this->fileName;
    }

    /**
     * @param string $emailAddress
     *
     * @return $this
     */
    public function setFileName($fileName)
    {
        $this->fileName = $fileName;
        return $this;
    }

	/**
     * @return string
     */
    public function getFileFormat()
    {
        return $this->fileFormat;
    }

    /**
     * @param string $fileFormat
     *
     * @return $this
     */
    public function setFileFormat($fileFormat)
    {
        $this->fileFormat = $fileFormat;
        return $this;
    }

	/**
     * @return string
     */
    public function getDocumentType()
    {
        return $this->documentType;
    }

    /**
     * @param string $documentType
     *
     * @return $this
     */
    public function setDocumentType($documentType)
    {
        $this->documentType = $documentType;
        return $this;
    }

	/**
     * @return string
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * @param string $file Base64Binary file string
     *
     * @return $this
     */
    public function setFile($file)
    {
        $this->file = $file;
        return $this;
    }

	/**
     * @param string $fileFormat
     *
     * @return bool
     */
	public static function isAllowedFileFormat($fileFormat) {
		return in_array( $fileFormat, self::ALLOWED_FILE_FORMATS, true );
	}
}
