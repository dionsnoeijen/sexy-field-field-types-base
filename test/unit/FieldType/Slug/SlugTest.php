<?php
declare (strict_types=1);

namespace Tardigrades\FieldType\Slug;

use PHPUnit\Framework\TestCase;
use Mockery as M;
use Symfony\Component\Form\FormBuilderInterface;
use Tardigrades\Entity\SectionInterface;
use Tardigrades\FieldType\FieldType;
use Tardigrades\SectionField\Service\ReadSectionInterface;
use Tardigrades\SectionField\Service\SectionManagerInterface;
use Tardigrades\SectionField\ValueObject\FieldConfig;

/**
 * @coversDefaultClass Tardigrades\FieldType\Slug\Slug
 * @covers ::<private>
 */
class SlugTest extends TestCase
{
    /**
     * @test
     * @covers ::addToForm
     */
    public function it_adds_to_form()
    {
        // this test does not actually test for nothing because the Slug class is not yet complete. But nevertheless,
        // is the test already set up.

        $formBuilder = M::mock(FormBuilderInterface::class);
        $section = M::mock(SectionInterface::class);
        $sectionEntity = M::mock(FieldType::class);
        $sectionManager = M::mock(SectionManagerInterface::class);
        $readSection = M::mock(ReadSectionInterface::class);

        $slug = new Slug();
        $config = FieldConfig::fromArray(
            [
                'field' =>
                    [
                        'name' => 'sexyname',
                        'handle' => 'Jiggling TurkeyMoan',
                    ]
            ]
        );
        $slug->setConfig($config);

        $slug->addToForm($formBuilder, $section, $sectionEntity, $sectionManager, $readSection);

        $this->assertInstanceOf(Slug::class, $slug);
        $this->assertEquals($slug->getConfig(), $config);
    }
}
