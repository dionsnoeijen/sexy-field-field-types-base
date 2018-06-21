<?php
declare (strict_types=1);

namespace Tardigrades\FieldType\DateTime;

use Mockery as M;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\Request;
use Tardigrades\Entity\SectionInterface;
use Tardigrades\FieldType\DateTimeTimezone\DateTimeTimezone;
use Tardigrades\SectionField\Generator\CommonSectionInterface;
use Tardigrades\SectionField\Service\ReadSectionInterface;
use Tardigrades\SectionField\Service\SectionManagerInterface;
use Tardigrades\SectionField\ValueObject\FieldConfig;

/**
 * @coversDefaultClass Tardigrades\FieldType\DateTimeTimezone\DateTimeTimezone
 * @covers ::<private>
 */
class DateTimeTimezoneTest extends TestCase
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

        $dateTimeField = new DateTimeTimezone();
        $config = FieldConfig::fromArray(
            [
                'field' =>
                    [
                        'name' => 'sexyname',
                        'handle' => 'lovehandles',
                        'form' => [
                            'all' => [
                                'label' => 'a label',
                                'format' => DateTimeType::HTML5_FORMAT,
                                'data' => '2015-12-25'
                            ]
                        ]
                    ]
            ]
        );
        $dateTimeField->setConfig($config);
        $sectionEntity->shouldReceive('getId')
            ->once()
            ->andReturn(4);

        $formBuilder->shouldReceive('add')
            ->once()
            ->with(
                (string)$dateTimeField->getConfig()->getHandle(),
                DateTimeType::class,
                [
                    'label' => 'a label',
                    'format' => DateTimeType::HTML5_FORMAT,
                    'data'=>new \DateTime('2015-12-25')
                ]
            )
            ->andReturn($formBuilder);

        $formBuilder->shouldReceive('add')
            ->once()
            ->with(
                (string)$dateTimeField->getConfig()->getHandle().'Timezone',
                TextType::class,
                [
                    'required' => false,
                    'empty_data' => 'Europe/Amsterdam'
                ]
            )
            ->andReturn($formBuilder);

        $dateTimeField->addToForm(
            $formBuilder, $section, $sectionEntity,
            $sectionManager, $readSection, $request
        );

        $this->assertInstanceOf(DateTimeTimezone::class, $dateTimeField);
        $this->assertEquals($dateTimeField->getConfig(), $config);
    }

    /**
     * @test
     * @covers ::addToForm
     */
    public function it_adds_to_form_even_if_no_format_or_data_key_in_config()
    {
        $formBuilder = M::mock(FormBuilderInterface::class);
        $section = M::mock(SectionInterface::class);
        $sectionEntity = M::mock(CommonSectionInterface::class);
        $sectionManager = M::mock(SectionManagerInterface::class);
        $readSection = M::mock(ReadSectionInterface::class);
        $request = M::mock(Request::class);

        $dateTimeField = new DateTimeTimezone();
        $config = FieldConfig::fromArray(
            [
                'field' =>
                    [
                        'name' => 'sexyname',
                        'handle' => 'lovehandles',
                        'form' => ['all' => ['label' => 'a label']]
                    ]
            ]
        );
        $dateTimeField->setConfig($config);
        $sectionEntity->shouldReceive('getId')
            ->once()
            ->andReturn(4);

        $formBuilder->shouldReceive('add')
            ->twice()
            ->andReturn($formBuilder);

        $dateTimeField->addToForm(
            $formBuilder, $section, $sectionEntity,
            $sectionManager, $readSection, $request
        );

        $this->assertInstanceOf(DateTimeTimezone::class, $dateTimeField);
        $this->assertEquals($dateTimeField->getConfig(), $config);
    }
}
