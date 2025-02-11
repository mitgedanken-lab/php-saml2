<?php

declare(strict_types=1);

namespace SimpleSAML\Test\SAML2\XML\saml;

use PHPUnit\Framework\TestCase;
use SimpleSAML\Assert\AssertionFailedException;
use SimpleSAML\SAML2\Constants as C;
use SimpleSAML\SAML2\XML\saml\Issuer;
use SimpleSAML\XML\DOMDocumentFactory;
use SimpleSAML\XML\TestUtils\SchemaValidationTestTrait;
use SimpleSAML\XML\TestUtils\SerializableElementTestTrait;

use function dirname;
use function strval;

/**
 * Class \SAML2\XML\saml\IssuerTest
 *
 * @covers \SimpleSAML\SAML2\XML\saml\Issuer
 * @covers \SimpleSAML\SAML2\XML\saml\NameIDType
 * @covers \SimpleSAML\SAML2\XML\saml\AbstractSamlElement
 * @package simplesamlphp/saml2
 */
final class IssuerTest extends TestCase
{
    use SchemaValidationTestTrait;
    use SerializableElementTestTrait;

    /**
     */
    public static function setUpBeforeClass(): void
    {
        self::$schemaFile = dirname(__FILE__, 5) . '/resources/schemas/saml-schema-assertion-2.0.xsd';

        self::$testedClass = Issuer::class;

        self::$xmlRepresentation = DOMDocumentFactory::fromFile(
            dirname(__FILE__, 4) . '/resources/xml/saml_Issuer.xml',
        );
    }


    // marshalling


    /**
     */
    public function testMarshalling(): void
    {
        $issuer = new Issuer(
            'TheIssuerValue',
            'TheNameQualifier',
            'TheSPNameQualifier',
            'urn:the:format',
            'TheSPProvidedID',
        );

        $this->assertEquals(
            self::$xmlRepresentation->saveXML(self::$xmlRepresentation->documentElement),
            strval($issuer),
        );
    }


    /**
     * Test that creating an Issuer from scratch contains no attributes when format is "entity".
     */
    public function testMarshallingEntityFormat(): void
    {
        $this->expectException(AssertionFailedException::class);
        $this->expectExceptionMessage('Illegal combination of attributes being used');

        new Issuer(
            'TheIssuerValue',
            'TheNameQualifier',
            'TheSPNameQualifier',
            C::NAMEID_ENTITY,
            'TheSPProvidedID',
        );
    }


    /**
     * Test that creating an Issuer from scratch with no format defaults to "entity", and it therefore contains no other
     * attributes.
     */
    public function testMarshallingNoFormat(): void
    {
        $this->expectException(AssertionFailedException::class);
        $this->expectExceptionMessage('Illegal combination of attributes being used');

        new Issuer(
            value: 'TheIssuerValue',
            NameQualifier: 'TheNameQualifier',
            SPNameQualifier: 'TheSPNameQualifier',
            SPProvidedID: 'TheSPProvidedID',
        );
    }


    // unmarshalling


    /**
     * Test that creating an Issuer from XML contains no attributes when format is "entity".
     */
    public function testUnmarshallingEntityFormat(): void
    {
        $xmlRepresentation = clone self::$xmlRepresentation;
        $xmlRepresentation->documentElement->setAttribute('Format', C::NAMEID_ENTITY);

        $this->expectException(AssertionFailedException::class);
        $this->expectExceptionMessage('Illegal combination of attributes being used');

        Issuer::fromXML($xmlRepresentation->documentElement);
    }


    /**
     * Test that creating an Issuer from XML contains no attributes when there's no format (defaults to "entity").
     */
    public function testUnmarshallingNoFormat(): void
    {
        $xmlRepresentation = clone self::$xmlRepresentation;
        $xmlRepresentation->documentElement->removeAttribute('Format');

        $this->expectException(AssertionFailedException::class);
        $this->expectExceptionMessage('Illegal combination of attributes being used');

        Issuer::fromXML($xmlRepresentation->documentElement);
    }
}
