<?php declare(strict_types = 1);

namespace WebChemistry\OAuth2\Client\Seznam\Provider;

use League\OAuth2\Client\Provider\AbstractProvider;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use League\OAuth2\Client\Token\AccessToken;
use Psr\Http\Message\ResponseInterface;

final class Seznam extends AbstractProvider
{

	/**
	 * @return string[]
	 */
	protected function getDefaultHeaders(): array
	{
		return [
			'Accept' => 'application/json',
		];
	}

	/**
	 * @return mixed[]
	 */
	protected function getAuthorizationHeaders(mixed $token = null): array
	{
		if (!$token instanceof AccessToken) {
			return parent::getAuthorizationHeaders($token);
		}

		return [
			'Authorization' => 'Bearer ' . $token->getToken(),
		];
	}

	public function getBaseAuthorizationUrl(): string
	{
		return 'https://login.szn.cz/api/v1/oauth/auth';
	}

	public function getBaseAccessTokenUrl(array $params): string
	{
		return 'https://login.szn.cz/api/v1/oauth/token';
	}

	public function getResourceOwnerDetailsUrl(AccessToken $token): string
	{
		return 'https://login.szn.cz/api/v1/user';
	}

	protected function getDefaultScopes()
	{
		return [
			'identity',
		];
	}

	protected function checkResponse(ResponseInterface $response, $data): void
	{
		$message = $data['message'] ?? 'unknown';
		$code = $data['status'] ?? 0;

		if ($message !== 'ok' || $code !== 200) {
			throw new IdentityProviderException($message, $code, $data);
		}
	}

	protected function createResourceOwner(array $response, AccessToken $token)
	{
		return new SeznamUser($response, $token->getValues()['account_name'] ?? null);
	}

}
