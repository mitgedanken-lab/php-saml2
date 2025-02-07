<?php

declare(strict_types=1);

namespace SimpleSAML\SAML2\Test\SAML2\XML\saml;

use DOMDocument;
use PHPUnit\Framework\TestCase;
use SimpleSAML\SAML2\XML\Decision;
use SimpleSAML\SAML2\XML\saml\Action;
use SimpleSAML\SAML2\XML\saml\AuthzDecisionStatement;
use SimpleSAML\SAML2\XML\saml\Evidence;
use SimpleSAML\XML\DOMDocumentFactory;
use SimpleSAML\XML\TestUtils\SchemaValidationTestTrait;
use SimpleSAML\XML\TestUtils\SerializableElementTestTrait;

use function dirname;
use function strval;

/**
 * Class \SimpleSAML\SAML2\XML\saml\AuthzDecisionStatementTest
 *
 * @covers \SimpleSAML\SAML2\XML\saml\AuthzDecisionStatement
 * @covers \SimpleSAML\SAML2\XML\saml\AbstractStatement
 * @covers \SimpleSAML\SAML2\XML\saml\AbstractSamlElement
 *
 * @package simplesamlphp/saml2
 */
final class AuthzDecisionStatementTest extends TestCase
{
    use SchemaValidationTestTrait;
    use SerializableElementTestTrait;

    /** @var \DOMDocument $evidence */
    private static DOMDocument $evidence;


    /**
     */
    public static function setUpBeforeClass(): void
    {
        self::$schemaFile = dirname(__FILE__, 5) . '/resources/schemas/saml-schema-assertion-2.0.xsd';

        self::$testedClass = AuthzDecisionStatement::class;

        self::$xmlRepresentation = DOMDocumentFactory::fromFile(
            dirname(__FILE__, 4) . '/resources/xml/saml_AuthzDecisionStatement.xml',
        );

        self::$evidence = DOMDocumentFactory::fromFile(
            dirname(__FILE__, 4) . '/resources/xml/saml_Evidence.xml',
        );
    }


    /**
     */
    public function testMarshalling(): void
    {
        $authzDecisionStatement = new AuthzDecisionStatement(
            'urn:x-simplesamlphp:resource',
            Decision::PERMIT,
            [
                new Action('urn:x-simplesamlphp:namespace', 'SomeAction'),
                new Action('urn:x-simplesamlphp:namespace', 'OtherAction'),
            ],
            Evidence::fromXML(self::$evidence->documentElement),
        );

        $this->assertEquals(
            self::$xmlRepresentation->saveXML(self::$xmlRepresentation->documentElement),
            strval($authzDecisionStatement),
        );
    }
}
