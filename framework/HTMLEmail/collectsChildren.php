<?php

namespace HTMLEmail;

use NodeBuilder\Extensions\ElemNode;
use NodeBuilder\NodeBuilder;

trait collectsChildren
{

	/**
	 * @param NodeBuilder[] $children_
	 * @return $this
	 */
	public function add(...$children_):self
	{
		$this->domCollection->addChildren($children_);
		return $this;
	}

	public function addRow(NodeBuilder $_child):self
	{
		return $this->add(HTMLEmail::row($_child));
	}

	public function addImgRow($src, string $alt = null, string $href = null):self
	{
		if ( is_array($src) ) {
			$src = $src['src'];
			$alt = $src['alt'] ?? null;
			$href = $src['href'] ?? null;
		}
		$img_attrs = [
			'style' => "display:block",
			'width' => $this->getContainer()->getWidth(),
			'class' => 'w-100p',
		];
		$elemNode = $href ?
			static::buildImgLink($src, $alt, $href, $img_attrs) :
			static::buildImg($src, $alt, $img_attrs);
		return $this->add(HTMLEmail::row($elemNode));
	}

	/**
	 * @param NodeBuilder[]|array $children_
	 * @return $this
	 */
	public function addColumns(...$children_):self
	{
		$children_ = array_map(
			fn($_child) => is_array($_child) ?
				ElemNode::new('td')
				        ->attrs($_child[0])
				        ->addChildren($_child[1]) :
				$_child,
			$children_
		);
		return $this->add(
			ElemNode::new('tr')->addChild(
				ElemNode::new('td')->addChild(
					HTMLEmail::buildTable()->addChild(
						ElemNode::new('tr')->addChildren(
							$children_
						)
					)
				)
			)
		);
	}

	/**
	 * @param string[] $srcs
	 */
	public function addTrackingPixels(array $srcs):self
	{
		$imgs_ = array_map(
			fn($src) => HTMLEmail::img([
				'src'    => $src,
				'height' => 1,
				'width'  => 1,
			]),
			$srcs
		);
		return $this->add(
			ElemNode::new('tr')->addChild(
				ElemNode::new('td')->addChildren($imgs_)
			)
		);
	}

	public function addPadded(array $padding, array $children_):self
	{
		return $this->add(
			HTMLEmail::buildPadded($padding, $children_)
		);
	}

}
