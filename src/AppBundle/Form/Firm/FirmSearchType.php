<?php

namespace AppBundle\Form\Firm;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * The firmSearchtype is a search form for firms.
 */
class FirmSearchType extends AbstractType {

    /**
     * Build the form.
     *
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder->setMethod('get');

        $builder->add('name', TextType::class, array(
            'label' => 'Search Firms by Name',
            'required' => false,
            'attr' => array(
                'help_block' => 'Enter all or part of a firm name'
            ),
        ));

        $builder->add('id', TextType::class, array(
            'label' => 'Firm ID',
            'required' => false,
            'attr' => array(
                'help_block' => 'Find this exact firm ID.',
            )
        ));

        $builder->add('address', TextType::class, array(
            'label' => 'Address',
            'required' => false,
            'attr' => array(
                'help_block' => 'Text search for a firm address'
            ),
        ));

        $builder->add('city', TextType::class, array(
            'label' => 'City',
            'required' => false,
            'attr' => array(
                'help_block' => 'Text search for a firm city'
            ),
        ));
        $builder->add('start', TextType::class, array(
            'label' => 'Start date',
            'required' => false,
            'attr' => array(
                'help_block' => 'Enter a year (eg <kbd>1795</kbd>) or range of years (<kbd>1790-1800</kbd>) or a partial range of years (<kbd>*-1800</kbd>)',
            ),
        ));
        $builder->add('end', TextType::class, array(
            'label' => 'End Date',
            'required' => false,
            'attr' => array(
                'help_block' => 'Enter a year (eg <kbd>1795</kbd>) or range of years (<kbd>1790-1800</kbd>) or a partial range of years (<kbd>*-1800</kbd>)',
            ),
        ));

    }

    /**
     * {@inheritdoc}
     *
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver) {
        parent::configureOptions($resolver);
    }

}
