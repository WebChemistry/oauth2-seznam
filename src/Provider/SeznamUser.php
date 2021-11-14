<?php declare(strict_types = 1);

namespace WebChemistry\OAuth2\Client\Seznam\Provider;

use League\OAuth2\Client\Provider\ResourceOwnerInterface;
use LogicException;

class SeznamUser implements ResourceOwnerInterface
{

	/**
	 * @param mixed[] $response
	 */
	public function __construct(
		protected array $response,
		protected ?string $email,
	)
	{
	}

	public function getId(): string
	{
		return $this->getResponseStringValue('advert_user_id');
	}

	public function getUserName(): string
	{
		return $this->getResponseStringValue('username');
	}

	public function getDomain(): string
	{
		return $this->getResponseStringValue('domain');
	}

	public function getFirstName(): ?string
	{
		$firstName = $this->response['firstname'] ?? null;

		if (!is_string($firstName)) {
			return null;
		}

		$firstName = trim($firstName);

		if (!$firstName) {
			return null;
		}

		return $firstName;
	}

	public function getLastName(): ?string
	{
		$lastName = $this->response['lastname'] ?? null;

		if (!is_string($lastName)) {
			return null;
		}

		$lastName = trim($lastName);

		if (!$lastName) {
			return null;
		}

		return $lastName;
	}

	public function getName(): ?string
	{
		$firstName = $this->getFirstName();
		$lastName = $this->getLastName();

		if (!$firstName && !$lastName) {
			return null;
		}

		if ($firstName && $lastName) {
			return sprintf('%s %s', $firstName, $lastName);
		}

		return $firstName ?: $lastName;
	}

	public function getEmail(): string
	{
		if ($this->email) {
			return $this->email;
		}

		return sprintf('%s@%s', $this->getUserName(), $this->getDomain());
	}

	public function toArray(): array
	{
		return $this->response;
	}

	private function getResponseStringValue(string $key): string
	{
		$value = $this->response[$key] ?? throw new LogicException(sprintf('Unexpected error: missing %s in response.', $key));

		if (!is_string($value)) {
			throw new LogicException(
				sprintf(
					'Unexpected error: response value (%s) must be a string, %s given.',
					$key,
					get_debug_type($value)
				)
			);
		}

		return $value;
	}

}
