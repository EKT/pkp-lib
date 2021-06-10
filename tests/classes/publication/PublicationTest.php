<?php
/**
 * @file tests/classes/publication/PublicationTest.inc.php
 *
 * Copyright (c) 2013-2021 Simon Fraser University
 * Copyright (c) 2000-2021 John Willinsky
 * Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * @class PublicationTest
 * @ingroup tests_classes_publicatio
 *
 * @see Publication
 *
 * @brief Test class for the Publication class
 */

use APP\publication\DAO;
use PKP\citation\CitationDAO;
use PKP\context\CategoryDAO;
use PKP\services\PKPSchemaService;
use PKP\submission\SubmissionAgencyDAO;
use PKP\submission\SubmissionDisciplineDAO;
use PKP\submission\SubmissionKeywordDAO;
use PKP\submission\SubmissionLanguageDAO;
use PKP\submission\SubmissionSubjectDAO;

import('lib.pkp.tests.PKPTestCase');
class PublicationTest extends PKPTestCase
{
    /**
     * @see PKPTestCase::setUp()
     */
    protected function setUp(): void
    {
        $this->publication = (new DAO(
            new SubmissionKeywordDAO(),
            new SubmissionSubjectDAO(),
            new SubmissionDisciplineDAO(),
            new SubmissionLanguageDAO(),
            new SubmissionAgencyDAO(),
            new CategoryDAO(),
            new CitationDAO(),
            new PKPSchemaService()
        ))->newDataObject();
    }
    /**
     * @see PKPTestCase::tearDown()
     */
    protected function tearDown(): void
    {
        unset($this->publication);
    }
    //
    // Unit tests
    //
    /**
     * @covers publication
     */
    public function testPageArray()
    {
        $expected = [['i', 'ix'], ['6', '11'], ['19'], ['21']];
        // strip prefix and spaces
        $this->publication->setData('pages', 'pg. i-ix, 6-11, 19, 21');
        $pageArray = $this->publication->getPageArray();
        $this->assertSame($expected, $pageArray);
        // no spaces
        $this->publication->setData('pages', 'i-ix,6-11,19,21');
        $pageArray = $this->publication->getPageArray();
        $this->assertSame($expected, $pageArray);
        // double-hyphen
        $this->publication->setData('pages', 'i--ix,6--11,19,21');
        $pageArray = $this->publication->getPageArray();
        $this->assertSame($expected, $pageArray);
        // single page
        $expected = [['16']];
        $this->publication->setData('pages', '16');
        $pageArray = $this->publication->getPageArray();
        $this->assertSame($expected, $pageArray);
        // spaces in a range
        $expected = [['16', '20']];
        $this->publication->setData('pages', '16 - 20');
        $pageArray = $this->publication->getPageArray();
        $this->assertSame($expected, $pageArray);
        // pages are alphanumeric
        $expected = [['a6', 'a12'], ['b43']];
        $this->publication->setData('pages', 'a6-a12,b43');
        $pageArray = $this->publication->getPageArray();
        $this->assertSame($expected, $pageArray);
        // inconsisent formatting
        $this->publication->setData('pages', 'pp:  a6 -a12,   b43');
        $pageArray = $this->publication->getPageArray();
        $this->assertSame($expected, $pageArray);
        $this->publication->setData('pages', '  a6 -a12,   b43 ');
        $pageArray = $this->publication->getPageArray();
        $this->assertSame($expected, $pageArray);
        // empty-ish values
        $expected = [];
        $this->publication->setData('pages', '');
        $pageArray = $this->publication->getPageArray();
        $this->assertSame($expected, $pageArray);
        $this->publication->setData('pages', ' ');
        $pageArray = $this->publication->getPageArray();
        $this->assertSame($expected, $pageArray);
        $expected = [['0']];
        $this->publication->setData('pages', '0');
        $pageArray = $this->publication->getPageArray();
        $this->assertSame($expected, $pageArray);
    }

    /**
     * @covers publication
     */
    public function testGetStartingPage()
    {
        $expected = 'i';
        // strip prefix and spaces
        $this->publication->setData('pages', 'pg. i-ix, 6-11, 19, 21');
        $startingPage = $this->publication->getStartingPage();
        $this->assertSame($expected, $startingPage);
        // no spaces
        $this->publication->setData('pages', 'i-ix,6-11,19,21');
        $startingPage = $this->publication->getStartingPage();
        $this->assertSame($expected, $startingPage);
        // double-hyphen
        $this->publication->setData('pages', 'i--ix,6--11,19,21');
        $startingPage = $this->publication->getStartingPage();
        $this->assertSame($expected, $startingPage);
        // single page
        $expected = '16';
        $this->publication->setData('pages', '16');
        $startingPage = $this->publication->getStartingPage();
        $this->assertSame($expected, $startingPage);
        // spaces in a range
        $this->publication->setData('pages', '16 - 20');
        $startingPage = $this->publication->getStartingPage();
        $this->assertSame($expected, $startingPage);
        // pages are alphanumeric
        $expected = 'a6';
        $this->publication->setData('pages', 'a6-a12,b43');
        $startingPage = $this->publication->getStartingPage();
        $this->assertSame($expected, $startingPage);
        // inconsisent formatting
        $this->publication->setData('pages', 'pp:  a6 -a12,   b43');
        $startingPage = $this->publication->getStartingPage();
        $this->assertSame($expected, $startingPage);
        $this->publication->setData('pages', '  a6 -a12,   b43 ');
        $startingPage = $this->publication->getStartingPage();
        $this->assertSame($expected, $startingPage);
        // empty-ish values
        $expected = '';
        $this->publication->setData('pages', '');
        $startingPage = $this->publication->getStartingPage();
        $this->assertSame($expected, $startingPage);
        $this->publication->setData('pages', ' ');
        $startingPage = $this->publication->getStartingPage();
        $this->assertSame($expected, $startingPage);
        $expected = '0';
        $this->publication->setData('pages', '0');
        $startingPage = $this->publication->getStartingPage();
        $this->assertSame($expected, $startingPage);
    }

    /**
     * @covers publication
     */
    public function testGetEndingPage()
    {
        $expected = '21';
        // strip prefix and spaces
        $this->publication->setData('pages', 'pg. i-ix, 6-11, 19, 21');
        $endingPage = $this->publication->getEndingPage();
        $this->assertSame($expected, $endingPage);
        // no spaces
        $this->publication->setData('pages', 'i-ix,6-11,19,21');
        $endingPage = $this->publication->getEndingPage();
        $this->assertSame($expected, $endingPage);
        // double-hyphen
        $this->publication->setData('pages', 'i--ix,6--11,19,21');
        $endingPage = $this->publication->getEndingPage();
        $this->assertSame($expected, $endingPage);
        // single page
        $expected = '16';
        $this->publication->setData('pages', '16');
        $endingPage = $this->publication->getEndingPage();
        $this->assertSame($expected, $endingPage);
        // spaces in a range
        $expected = '20';
        $this->publication->setData('pages', '16 - 20');
        $endingPage = $this->publication->getEndingPage();
        $this->assertSame($expected, $endingPage);
        // pages are alphanumeric
        $expected = 'b43';
        $this->publication->setData('pages', 'a6-a12,b43');
        $endingPage = $this->publication->getEndingPage();
        $this->assertSame($expected, $endingPage);
        // inconsisent formatting
        $this->publication->setData('pages', 'pp:  a6 -a12,   b43');
        $endingPage = $this->publication->getEndingPage();
        $this->assertSame($expected, $endingPage);
        $this->publication->setData('pages', '  a6 -a12,   b43 ');
        $endingPage = $this->publication->getEndingPage();
        $this->assertSame($expected, $endingPage);
        // empty-ish values
        $expected = '';
        $this->publication->setData('pages', '');
        $endingPage = $this->publication->getEndingPage();
        $this->assertSame($expected, $endingPage);
        $this->publication->setData('pages', ' ');
        $endingPage = $this->publication->getEndingPage();
        $this->assertSame($expected, $endingPage);
        $expected = '0';
        $this->publication->setData('pages', '0');
        $endingPage = $this->publication->getEndingPage();
        $this->assertSame($expected, $endingPage);
    }
}
