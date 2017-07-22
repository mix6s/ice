<?php
/**
 * Created by PhpStorm.
 * User: Mix6s
 * Date: 22.07.2017
 * Time: 14:38
 */

namespace AppBundle\Form\Type;


use AppBundle\Entity\PlayerProfile;
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
						'Вратарь' => PlayerProfile::POSITION_GK,
						'Левый защитник' => PlayerProfile::POSITION_LB,
						'Правый защитник' => PlayerProfile::POSITION_RB,
						'Центральный нападающий' => PlayerProfile::POSITION_CF,
						'Левый нападающий' => PlayerProfile::POSITION_LF,
						'Правый нападающий' => PlayerProfile::POSITION_RF,
					],
					'required' => false
				]
			)
			->add(
				'stick',
				ChoiceType::class,
				[
					'choices' => [
						'Левый' => PlayerProfile::STICK_L,
						'Правый' => PlayerProfile::STICK_R,
					],
					'required' => false
				]
			);

		$builder
			->get('birthdate')
			->addModelTransformer(new CallbackTransformer(
				function (\DateTime $asDateTime = null) {
					return $asDateTime ? $asDateTime->format('d.m.Y') : '';
				},
				function ($asString) {
					return new \DateTime($asString);
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
				'data_class' => PlayerProfile::class,
				'csrf_token_id' => 'PlayerProfile',
				'method' => 'POST'
			]
		);
	}
}