<?php
declare (strict_types=1);

namespace Tardigrades\FieldType\TextArea;

use PHPUnit\Framework\TestCase;
use Mockery as M;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Tardigrades\Entity\SectionInterface;
use Tardigrades\FieldType\FieldType;
use Tardigrades\SectionField\Service\ReadSectionInterface;
use Tardigrades\SectionField\Service\SectionManagerInterface;
use Tardigrades\SectionField\ValueObject\FieldConfig;

/**
 * @coversDefaultClass Tardigrades\FieldType\TextArea\TextArea
 * @covers ::<private>
 */
class TextAreaTest extends TestCase
{
    /**
     * @test
     * @covers ::addToForm
     */
    public function it_adds_to_form()
    {
        $formBuilder = M::mock(FormBuilderInterface::class);
        $section = M::mock(SectionInterface::class);
        $sectionEntity = M::mock(FieldType::class);
        $sectionManager = M::mock(SectionManagerInterface::class);
        $readSection = M::mock(ReadSectionInterface::class);

        $textArea = new TextArea();
        $config = FieldConfig::fromArray(
            [
                'field' =>
                    [
                        'name' => 'sexyname',
                        'handle' => 'Frostbeard MilkSwallow',
                    ]
            ]
        );
        $textArea->setConfig($config);
        $sectionEntity->shouldReceive('getId')
            ->once()
            ->andReturn(2001);

        $formBuilder->shouldReceive('add')
            ->once()
            ->with('Frostbeard MilkSwallow', TextareaType::class)
            ->andReturn($formBuilder);

        $textArea->addToForm($formBuilder, $section, $sectionEntity, $sectionManager, $readSection);

        $this->assertInstanceOf(TextArea::class, $textArea);
        $this->assertEquals($textArea->getConfig(), $config);
    }
}
