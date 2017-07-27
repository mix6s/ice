<?php
/**
 * Created by PhpStorm.
 * User: Mix6s
 * Date: 22.07.2017
 * Time: 14:38
 */

namespace AppBundle\Form\Type;


use DomainBundle\Entity\PlayerMetadata;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class PlayerProfileFormType
 * @package AppBundle\Form\Type
 */
class PlayerProfileFormType extends AbstractType
{
	/**
	 * @param FormBuilderInterface $builder
	 * @param array $options
	 */
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
			->add(
				'firstName',
				TextType::class,
				[
					'required' => false
				]
			)
			->add(
				'secondName',
				TextType::class,
				[
					'required' => false
				]
			)
			->add(
				'surname',
				TextType::class,
				[
					'required' => false
				]
			)
			->add(
				'birthdate',
				TextType::class,
				[
					'required' => false
				]
			)
			->add(
				'height',
				NumberType::class,
				[
					'required' => false
				]
			)
			->add(
				'weight',
				NumberType::class,
				[
					'required' => false
				]
			)
			->add(
				'position',
				ChoiceType::class,
				[
					'choices' => [
						'Вратарь' => PlayerMetadata::POSITION_GK,
						'Левый защитник' => PlayerMetadata::POSITION_LB,
						'Правый защитник' => PlayerMetadata::POSITION_RB,
						'Центральный нападающий' => PlayerMetadata::POSITION_CF,
						'Левый нападающий' => PlayerMetadata::POSITION_LF,
						'Правый нападающий' => PlayerMetadata::POSITION_RF,
					],
				]
			)
			->add(
				'stick',
				ChoiceType::class,
				[
					'choices' => [
						'Левый' => PlayerMetadata::STICK_L,
						'Правый' => PlayerMetadata::STICK_R,
					],
				]
			);

		$builder
			->get('birthdate')
			->addModelTransformer(new CallbackTransformer(
				function (\DateTime $asDateTime = null) {
					return $asDateTime ? $asDateTime->format('d.m.Y') : '';
				},
				function ($asString = null) {
					return !empty($asString) ? new \DateTime($asString) : null;
				}
			))
		;
	}

	/**
	 * @param OptionsResolver $resolver
	 */
	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults(
			[
				'data_class' => PlayerMetadata::class,
				'csrf_token_id' => 'PlayerProfile',
				'method' => 'POST'
			]
		);
	}
}