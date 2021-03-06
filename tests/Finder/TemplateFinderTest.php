<?php

declare(strict_types=1);

namespace Assoconnect\MJMLBundle\Tests\Finder;

use Assoconnect\MJMLBundle\Finder\DuplicatesTemplatesNameException;
use Assoconnect\MJMLBundle\Finder\TemplateFinder;
use PHPUnit\Framework\TestCase;

class TemplateFinderTest extends TestCase
{
    public function testFindDefault()
    {
        $finder = new TemplateFinder(__DIR__ . '/../Functional', ['/templates/mjml/']);

        $templates = $finder->find();

        $this->assertCount(1, $templates);

        $template = array_pop($templates);
        $this->assertSame('custom.mjml.twig', $template->getFilename());
    }

    public function testFindOne()
    {
        $finder = new TemplateFinder(__DIR__ . '/../Functional', ['/templates/mjml/']);

        $templates = $finder->find('custom.mjml.twig');

        $this->assertCount(1, $templates);

        $template = array_pop($templates);
        $this->assertSame('custom.mjml.twig', $template->getFilename());
    }

    public function testFindTwo()
    {
        $finder = new TemplateFinder(__DIR__ . '/../Functional', [
            '/templates/mjml/',
            '/templates/folder/subfolder/mjml/'
        ]);

        $templates = $finder->find('custom.mjml.twig');

        //first template
        $this->assertCount(1, $templates);

        $template = array_pop($templates);
        $this->assertSame('custom.mjml.twig', $template->getFilename());

        //second template
        $templates = $finder->find('custom_path.mjml.twig');

        $this->assertCount(1, $templates);

        $template = array_pop($templates);
        $this->assertSame('custom_path.mjml.twig', $template->getFilename());
    }

    public function testFindDuplicates()
    {
        $finder = new TemplateFinder(__DIR__ . '/../Functional', [
            '/templates/mjml/',
            '/templates/folder/subfolder/mjml/',
            '/templates/folderWithDuplicates/subfolder/mjml/'
        ]);

        $this->expectException(DuplicatesTemplatesNameException::class);
        $this->expectExceptionMessage('Duplicates template name found custom_path.mjml.twig');
        $finder->find('custom_path.mjml.twig');
    }

    public function testNotFound()
    {
        $finder = new TemplateFinder(__DIR__ . '/../Functional', ['/templates/mjml/']);

        $templates = $finder->find('not_found.mjml.twig');

        $this->assertCount(0, $templates);
    }
}
