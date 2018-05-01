<?php
declare (strict_types=1);

namespace Tardigrades\FieldType\Collection;

use Mockery as M;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;
use Tardigrades\Entity\SectionInterface;
use Tardigrades\SectionField\Generator\CommonSectionInterface;
use Tardigrades\SectionField\Service\ReadSectionInterface;
use Tardigrades\SectionField\Service\SectionManagerInterface;
use Tardigrades\SectionField\ValueObject\FieldConfig;

/**
 * @coversDefaultClass Tardigrades\FieldType\Collection\Collection
 * @covers ::<private>
 */
class CollectionTest extends TestCase
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

        $collection = new Collection();
        $config = FieldConfig::fromArray([
            'field' => [
                'name' => 'I have a name',
                'handle' => 'iHaveAName',
                'form' => [
                    'all' => [
                        'entry_type' => EmailType::class
                    ]
                ]
            ]
        ]);
        $collection->setConfig($config);

        $formBuilder->shouldReceive('add')
            ->once()
            ->with(
                (string) $collection->getConfig()->getHandle(),
                CollectionType::class,
                [
                    'entry_type' => EmailType::class
                ]
            )
            ->andReturn($formBuilder);

        $sectionEntity->shouldReceive('getId')
            ->once()
            ->andReturn(4);

        $collection->addToForm(
            $formBuilder,
            $section,
            $sectionEntity,
            $sectionManager,
            $readSection
        );

        $this->assertInstanceOf(Collection::class, $collection);
        $this->assertEquals($collection->getConfig(), $config);
    }
}
