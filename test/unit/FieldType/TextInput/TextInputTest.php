<?php
declare (strict_types=1);

namespace Tardigrades\FieldType\TextInput;

use PHPUnit\Framework\TestCase;
use Mockery as M;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\Request;
use Tardigrades\Entity\SectionInterface;
use Tardigrades\FieldType\FieldType;
use Tardigrades\SectionField\Generator\CommonSectionInterface;
use Tardigrades\SectionField\Service\ReadSectionInterface;
use Tardigrades\SectionField\Service\SectionManagerInterface;
use Tardigrades\SectionField\ValueObject\FieldConfig;

/**
 * @coversDefaultClass Tardigrades\FieldType\TextInput\TextInput
 * @covers ::<private>
 */
class TextInputTest extends TestCase
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
        $sectionEntity = M::mock(CommonSectionInterface::class);
        $sectionManager = M::mock(SectionManagerInterface::class);
        $readSection = M::mock(ReadSectionInterface::class);
        $request = M::mock(Request::class);

        $textInput = new TextInput();
        $config = FieldConfig::fromArray(
            [
                'field' =>
                    [
                        'name' => 'sexyname',
                        'handle' => 'lovehandles',
                        'form' => ['all' => ['Marinated BrightBalls']]
                    ]
            ]
        );
        $textInput->setConfig($config);
        $sectionEntity->shouldReceive('getId')
            ->once()
            ->andReturn(2001);

        $formBuilder->shouldReceive('add')
            ->once()
            ->with('lovehandles', TextType::class, ['Marinated BrightBalls'])
            ->andReturn($formBuilder);

        $textInput->addToForm(
            $formBuilder, $section, $sectionEntity,
            $sectionManager, $readSection, $request
        );

        $this->assertInstanceOf(TextInput::class, $textInput);
        $this->assertEquals($textInput->getConfig(), $config);
    }
}
