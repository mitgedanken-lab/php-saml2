<?php

declare(strict_types=1);

namespace SimpleSAML\Test\SAML2\XML\md;

use DateTimeImmutable;
use Exception;
use PHPUnit\Framework\TestCase;
use SimpleSAML\SAML2\XML\md\AffiliateMember;
use SimpleSAML\SAML2\XML\md\AffiliationDescriptor;
use SimpleSAML\SAML2\XML\md\KeyDescriptor;
use SimpleSAML\Test\SAML2\Constants as C;
use SimpleSAML\XML\Attribute as XMLAttribute;
use SimpleSAML\XML\DOMDocumentFactory;
use SimpleSAML\XML\Exception\MissingAttributeException;
use SimpleSAML\XML\Exception\SchemaViolationException;
use SimpleSAML\XML\TestUtils\SchemaValidationTestTrait;
use SimpleSAML\XML\TestUtils\SerializableElementTestTrait;
use SimpleSAML\XMLSecurity\TestUtils\SignedElementTestTrait;
use SimpleSAML\XMLSecurity\XML\ds\KeyInfo;
use SimpleSAML\XMLSecurity\XML\ds\KeyName;

use function dirname;
use function strval;

/**
 * Tests for the AffiliationDescriptor class.
 *
 * @covers \SimpleSAML\SAML2\XML\md\AbstractMdElement
 * @covers \SimpleSAML\SAML2\XML\md\AbstractSignedMdElement
 * @covers \SimpleSAML\SAML2\XML\md\AbstractMetadataDocument
 * @covers \SimpleSAML\SAML2\XML\md\AffiliationDescriptor
 * @package simplesamlphp/saml2
 */
final class AffiliationDescriptorTest extends TestCase
{
    use SchemaValidationTestTrait;
    use SerializableElementTestTrait;
    use SignedElementTestTrait;


    /**
     */
    public static function setUpBeforeClass(): void
    {
        self::$schemaFile = dirname(__FILE__, 5) . '/resources/schemas/saml-schema-metadata-2.0.xsd';

        self::$testedClass = AffiliationDescriptor::class;

        self::$xmlRepresentation = DOMDocumentFactory::fromFile(
            dirname(__FILE__, 4) . '/resources/xml/md_AffiliationDescriptor.xml',
        );
    }


    // test marshalling


    /**
     * Test creating an AffiliationDescriptor object from scratch.
     */
    public function testMarshalling(): void
    {
        $affiliationDescriptor = new AffiliationDescriptor(
            affiliationOwnerId: C::ENTITY_IDP,
            affiliateMember: [new AffiliateMember(C::ENTITY_SP), new AffiliateMember(C::ENTITY_OTHER)],
            ID: 'TheID',
            validUntil: new DateTimeImmutable('2009-02-13T23:31:30Z'),
            cacheDuration: 'PT5000S',
            keyDescriptor: [
                new KeyDescriptor(
                    new KeyInfo(
                        [
                            new KeyName('IdentityProvider.com SSO Key'),
                        ],
                    ),
                    'signing',
                ),
            ],
            namespacedAttribute: [new XMLAttribute(C::NAMESPACE, 'ssp', 'attr1', 'value1')],
        );

        $this->assertEquals(
            self::$xmlRepresentation->saveXML(self::$xmlRepresentation->documentElement),
            strval($affiliationDescriptor),
        );
    }


    /**
     * Test that creating an AffiliationDescriptor with an empty owner ID fails.
     */
    public function testMarhsallingWithEmptyOwnerID(): void
    {
        $this->expectException(SchemaViolationException::class);
        new AffiliationDescriptor(
            affiliationOwnerId: '',
            affiliateMember: [new AffiliateMember(C::ENTITY_SP), new AffiliateMember(C::ENTITY_OTHER)],
        );
    }


    /**
     * Test that creating an AffiliationDescriptor with an empty list of members fails.
     */
    public function testMarshallingWithEmptyMemberList(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('List of affiliated members must not be empty.');
        new AffiliationDescriptor(
            affiliationOwnerId: C::ENTITY_IDP,
            affiliateMember: [],
        );
    }


    // test unmarshalling


    /**
     * Test failure to create an AffiliationDescriptor from XML when there's no affiliation members.
     */
    public function testUnmarshallingWithoutMembers(): void
    {
        $mdNamespace = AffiliationDescriptor::NS;
        $entity_idp = C::ENTITY_IDP;

        $document = DOMDocumentFactory::fromString(<<<XML
<md:AffiliationDescriptor xmlns:md="{$mdNamespace}" affiliationOwnerID="{$entity_idp}" ID="TheID"
    validUntil="2009-02-13T23:31:30Z" cacheDuration="PT5000S">
</md:AffiliationDescriptor>
XML
        );
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('List of affiliated members must not be empty.');
        AffiliationDescriptor::fromXML($document->documentElement);
    }


    /**
     * Test failure to create an AffiliationDescriptor from XML when there's no owner specified.
     */
    public function testUnmarshallingWithoutOwner(): void
    {
        $mdNamespace = AffiliationDescriptor::NS;
        $entity_other = C::ENTITY_OTHER;
        $entity_sp = C::ENTITY_SP;

        $document = DOMDocumentFactory::fromString(<<<XML
<md:AffiliationDescriptor xmlns:md="{$mdNamespace}" ID="TheID"
    validUntil="2009-02-13T23:31:30Z" cacheDuration="PT5000S">
  <md:AffiliateMember>{$entity_sp}</md:AffiliateMember>
  <md:AffiliateMember>{$entity_other}</md:AffiliateMember>
</md:AffiliationDescriptor>
XML
        );

        $this->expectException(MissingAttributeException::class);
        $this->expectExceptionMessage("Missing 'affiliationOwnerID' attribute on md:AffiliationDescriptor.");
        AffiliationDescriptor::fromXML($document->documentElement);
    }
}
