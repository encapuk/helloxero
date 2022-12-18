<?php
/**
 * Created By : Mikhail Glushenko, Encap Systems, 2022
 */
namespace Encap\HelloXero\Block;

class HelloXero extends \Magento\Framework\View\Element\Template
{
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

    public function getHelloXeroData()
    {
        return 'HelloXero block file call successfully';
    }
}
?>
