<?php
declare (strict_types=1);

namespace Tardigrades\FieldType\Integer;

use PHPUnit\Framework\TestCase;
use Mockery as M;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Tardigrades\Entity\SectionInterface;
use Tardigrades\FieldType\FieldType;
use Tardigrades\SectionField\Generator\CommonSectionInterface;
use Tardigrades\SectionField\Service\ReadSectionInterface;
use Tardigrades\SectionField\Service\SectionManagerInterface;
use Tardigrades\SectionField\ValueObject\FieldConfig;

/**
 * @coversDefaultClass Tardigrades\FieldType\Integer\Integer
 * @covers ::<private>
 */
class IntegerTest extends TestCase
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

        $integer = new Integer();
        $config = FieldConfig::fromArray(
            [
                'field' =>
                    [
                        'name' => 'sexyname',
                        'handle' => 'lovehandles',
                        'form' => ['all' => ['IntegerTesting']]
                    ]
            ]
        );
        $integer->setConfig($config);
        $sectionEntity->shouldReceive('getId')
            ->once()
            ->andReturn(400002);

        $formBuilder->shouldReceive('add')
            ->once()
            ->with((string)$integer->getConfig()->getHandle(), IntegerType::class, ['IntegerTesting'])
            ->andReturn($formBuilder);
        $integer->addToForm($formBuilder, $section, $sectionEntity, $sectionManager, $readSection);

        $this->assertInstanceOf(Integer::class, $integer);
        $this->assertEquals($integer->getConfig(), $config);
    }
}
