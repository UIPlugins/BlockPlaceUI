<?php
declare(strict_types=1);

namespace ARTulloss\libBoolUI;

use pocketmine\form\Form;
use pocketmine\Player;

/**
 * Class YesNoForm
 * @package ARTulloss\libBoolUI
 */
class YesNoForm implements Form
{
	/** @var array $data */
	private $data;
	/** @var bool $flipped */
	private $flipped;
	/** @var $forcedInput */
	private $forcedInput;
	/** @var callable $callable */
	private $callable;

	// Constants

	public const YES = 0;
	public const NO = 1;

	public const PATH = 0;
	public const URL = 1;

	/**
	 * YesNoForm constructor.
	 * @param callable|null $callable
	 * @param string $title
	 */
	public function __construct(callable $callable = null, $title = '')
	{
		$this->callable = $callable;
		$this->data['type'] = "form";
		$this->data['title'] = $title;
	}

	/**
	 * @param callable $callable
	 */
	public function setCallable(Callable $callable) {
		$this->callable = $callable;
	}

	/**
	 * @return callable|null
	 */
	public function getCallable(): ?callable
	{
		return $this->callable;
	}

	/**
	 * @param string $title
	 */
	public function setTitle(string $title): void
	{
		$this->data['title'] = $title;
	}

	/**
	 * @return string
	 */
	public function getTitle(): string
	{
		return $this->data['title'];
	}

	/**
	 * @param string $content
	 */
	public function setContent(string $content): void
	{
		$this->data['content'] = $content;
	}

	/**
	 * @return string
	 */
	public function getContent(): string
	{
		return $this->data['content'];
	}

	/**
	 * If forced, the player will have to hit yes or no to leave the form
	 * @param bool $forced
	 */
	public function setForced(bool $forced = true): void
	{
		$this->forcedInput = $forced;
	}

	/**
	 * @param bool $flipped
	 */
	public function setFlipped($flipped = true): void {
		$this->flipped = $flipped;
	}

	/**
	 * Randomize the button order!
	 */
	public function randomize(): void
	{
		$this->flipped = mt_rand(0,1);
	}

	/**
	 * Must come before you set the buttons images
	 * @param array $yesNo
	 */
	public function registerButtons(array $yesNo = ['Yes', 'No']): void
	{
		$this->data["buttons"][YesNoForm::YES] = ["text" => $this->flipped ? $yesNo[YesNoForm::NO] : $yesNo[YesNoForm::YES]];
		$this->data["buttons"][YesNoForm::NO] = ["text" => $this->flipped ? $yesNo[YesNoForm::YES] : $yesNo[YesNoForm::NO]];
	}

	/**
	 * Set the image of the button
	 * IMPORTANT:  this must come after you register the buttons
	 * @param int $yesOrNo
	 * @param bool $isURL
	 * @param string $imageURL
	 */
	public function setImage(int $yesOrNo, bool $isURL, string $imageURL): void
	{
		if($this->flipped)
			$yesOrNo = !$yesOrNo;

		$this->data['buttons'][$yesOrNo]['image']['type'] = $isURL ? 'url' : 'path';
		$this->data['buttons'][$yesOrNo]['image']['data'] = $imageURL;
	}

	/**
	 * For a callable, $data will be true if they hit yes, false if they hit no
	 *
	 * @param Player $player
	 * @param mixed $data
	 */
	public function handleResponse(Player $player, $data): void
	{
		if($this->forcedInput && $data === null)
			$player->sendForm($this);
		else {
			$data = (bool) !$data; // Flip because it makes more sense for a yes no form to return true if they hit yes and false if they hit false

			if($this->flipped)
				$data = !$data;

			$callable = $this->getCallable();

			if($callable !== null)
				$callable($player, $data);
		}
	}

	/**
	 * @return array
	 */
	public function jsonSerialize(): array
	{
		return $this->data;
	}

}
