<?php declare(strict_types=1);

namespace TEC\Common\LiquidWeb\LicensingApiClient\Http;

use InvalidArgumentException;
use TEC\Common\LiquidWeb\LicensingApiClient\Exceptions\MissingAuthenticationException;
use TEC\Common\LiquidWeb\LicensingApiClient\Value\AuthToken;

/**
 * Combines auth policy and configured token state for request execution.
 */
final class AuthState
{
	private AuthContext $authContext;

	private ?AuthToken $token;

	/**
	 * @throws InvalidArgumentException
	 */
	public function __construct(AuthContext $authContext, ?AuthToken $token = null) {
		if ($authContext->mode() === AuthContext::MODE_EXPLICIT && $token === null) {
			throw new InvalidArgumentException('Explicit auth mode requires a token.');
		}

		$this->authContext = $authContext;
		$this->token       = $token;
	}

	/**
	 * @throws MissingAuthenticationException
	 */
	public function optionalToken(): ?AuthToken {
		if ($this->authContext->mode() === AuthContext::MODE_NONE) {
			return null;
		}

		if ($this->authContext->mode() === AuthContext::MODE_CONFIGURED && $this->token === null) {
			throw new MissingAuthenticationException(
				'This request requires authentication, but no token is available.'
			);
		}

		return $this->token;
	}

	/**
	 * @throws MissingAuthenticationException
	 */
	public function requiredToken(): AuthToken {
		$token = $this->optionalToken();

		if ($token === null) {
			throw new MissingAuthenticationException(
				'This request requires authentication, but no token is available.'
			);
		}

		return $token;
	}

	public function withoutAuth(): self {
		return $this->withAuthContext(new AuthContext(AuthContext::MODE_NONE), $this->token);
	}

	public function withConfiguredToken(): self {
		return $this->withAuthContext(new AuthContext(AuthContext::MODE_CONFIGURED), $this->token);
	}

	public function withToken(string $token): self {
		return $this->withAuthContext(
			new AuthContext(AuthContext::MODE_EXPLICIT),
			new AuthToken($token)
		);
	}

	public function authContext(): AuthContext {
		return $this->authContext;
	}

	public function token(): ?AuthToken {
		return $this->token;
	}

	private function withAuthContext(AuthContext $authContext, ?AuthToken $token): self {
		if (
			$this->authContext->equals($authContext)
			&& (
				($this->token === null && $token === null)
				|| ($this->token !== null && $token !== null && $this->token->equals($token))
			)
		) {
			return $this;
		}

		return new self($authContext, $token);
	}
}
