<?php
declare (strict_types=1);

namespace Tardigrades\FieldType\Choice;

use PHPUnit\Framework\TestCase;
use Mockery as M;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\Request;
use Tardigrades\Entity\SectionInterface;
use Tardigrades\FieldType\FieldType;
use Tardigrades\SectionField\Generator\CommonSectionInterface;
use Tardigrades\SectionField\Service\ReadSectionInterface;
use Tardigrades\SectionField\Service\SectionManagerInterface;
use Tardigrades\SectionField\ValueObject\FieldConfig;

/**
 * @coversDefaultClass Tardigrades\FieldType\Choice\Choice
 * @covers ::<private>
 */
class ChoiceTest extends TestCase
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

        $choice = new Choice();
        $config = FieldConfig::fromArray(
            [
                'field' =>
                    [
                        'name' => 'sexyname',
                        'handle' => 'lovehandles',
                        'form' => ['all' => ['that']]
                    ]
            ]
        );
        $choice->setConfig($config);
        $sectionEntity->shouldReceive('getId')
            ->once()
            ->andReturn(4);

        $formBuilder->shouldReceive('add')
            ->once()
            ->with((string)$choice->getConfig()->getHandle(), ChoiceType::class, ['that'])
            ->andReturn($formBuilder);

        $choice->addToForm($formBuilder, $section, $sectionEntity, $sectionManager, $readSection, $request);

        $this->assertInstanceOf(Choice::class, $choice);
        $this->assertEquals($choice->getConfig(), $config);
    }
}
