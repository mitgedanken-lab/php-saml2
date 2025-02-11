<?php

declare(strict_types=1);

namespace SimpleSAML\Test\SAML2\XML\saml;

use PHPUnit\Framework\TestCase;
use SimpleSAML\SAML2\Constants as C;
use SimpleSAML\SAML2\XML\saml\SubjectLocality;
use SimpleSAML\XML\DOMDocumentFactory;
use SimpleSAML\XML\TestUtils\SchemaValidationTestTrait;
use SimpleSAML\XML\TestUtils\SerializableElementTestTrait;

use function dirname;
use function strval;

/**
 * Class \SAML2\XML\saml\SubjectLocalityTest
 *
 * @covers \SimpleSAML\SAML2\XML\saml\SubjectLocality
 * @covers \SimpleSAML\SAML2\XML\saml\AbstractSamlElement
 * @package simplesamlphp/saml2
 */
final class SubjectLocalityTest extends TestCase
{
    use SchemaValidationTestTrait;
    use SerializableElementTestTrait;

    /**
     */
    public static function setUpBeforeClass(): void
    {
        self::$schemaFile = dirname(__FILE__, 5) . '/resources/schemas/saml-schema-assertion-2.0.xsd';

        self::$testedClass = SubjectLocality::class;

        self::$xmlRepresentation = DOMDocumentFactory::fromFile(
            dirname(__FILE__, 4) . '/resources/xml/saml_SubjectLocality.xml',
        );
    }


    // marshalling


    /**
     */
    public function testMarshalling(): void
    {
        $subjectLocality = new SubjectLocality(
            '1.1.1.1',
            'idp.example.org',
        );

        $this->assertEquals(
            self::$xmlRepresentation->saveXML(self::$xmlRepresentation->documentElement),
            strval($subjectLocality),
        );
    }


    // unmarshalling


    /**
     * Adding no contents to a SubjectLocality element should yield an empty element. If there were contents already
     * there, those should be left untouched.
     */
    public function testMarshallingWithNoElements(): void
    {
        $samlns = C::NS_SAML;
        $subjectLocality = new SubjectLocality();
        $this->assertEquals(
            "<saml:SubjectLocality xmlns:saml=\"$samlns\"/>",
            strval($subjectLocality),
        );
        $this->assertTrue($subjectLocality->isEmptyElement());
    }
}
