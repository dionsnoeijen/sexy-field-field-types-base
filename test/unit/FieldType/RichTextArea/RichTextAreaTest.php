<?php
declare (strict_types=1);

namespace Tardigrades\FieldType\RichTextArea;

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
 * @coversDefaultClass Tardigrades\FieldType\RichTextArea\RichTextArea
 * @covers ::<private>
 */
class RichTextAreaTest extends TestCase
{
    use M\Adapter\Phpunit\MockeryPHPUnitIntegration;

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

        $richTextArea = new RichTextArea();
        $config = FieldConfig::fromArray(
            [
                'field' =>
                    [
                        'name' => 'sexyname',
                        'handle' => 'lovehandles',
                        'form' => ['all' => ['This is it']]
                    ]
            ]
        );
        $richTextArea->setConfig($config);
        $sectionEntity->shouldReceive('getId')
            ->once()
            ->andReturn(2001);

        $formBuilder->shouldReceive('add')
            ->once()
            ->with('lovehandles', TextareaType::class, ['This is it'])
            ->andReturn($formBuilder);

        $richTextArea->addToForm($formBuilder, $section, $sectionEntity, $sectionManager, $readSection);

        $this->assertInstanceOf(RichTextArea::class, $richTextArea);
        $this->assertEquals($richTextArea->getConfig(), $config);
    }
}
