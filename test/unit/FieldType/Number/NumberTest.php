<?php
declare (strict_types=1);

namespace Tardigrades\FieldType\Number;

use PHPUnit\Framework\TestCase;
use Mockery as M;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Tardigrades\Entity\SectionInterface;
use Tardigrades\FieldType\FieldType;
use Tardigrades\SectionField\Generator\CommonSectionInterface;
use Tardigrades\SectionField\Service\ReadSectionInterface;
use Tardigrades\SectionField\Service\SectionManagerInterface;
use Tardigrades\SectionField\ValueObject\FieldConfig;

/**
 * @coversDefaultClass Tardigrades\FieldType\Number\Number
 * @covers ::<private>
 */
class NumberTest extends TestCase
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

        $number = new Number();
        $config = FieldConfig::fromArray(
            [
                'field' =>
                    [
                        'name' => 'sexyname',
                        'handle' => 'lovehandles',
                        'form' => ['all' => ['NumberTesting']]
                    ]
            ]
        );
        $number->setConfig($config);
        $sectionEntity->shouldReceive('getId')
            ->once()
            ->andReturn(326542);

        $formBuilder->shouldReceive('add')
            ->once()
            ->with((string)$number->getConfig()->getHandle(), NumberType::class, ['NumberTesting'])
            ->andReturn($formBuilder);
        $number->addToForm($formBuilder, $section, $sectionEntity, $sectionManager, $readSection);

        $this->assertInstanceOf(Number::class, $number);
        $this->assertEquals($number->getConfig(), $config);
    }
}
