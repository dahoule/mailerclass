<?php

/**
 * mailer
 *
 * @author    Don Houle
 * @copyright 2023 Don Houle
 * @license   http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
 * @note      This program is distributed in the hope that it will be useful - WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or
 * FITNESS FOR A PARTICULAR PURPOSE.
 */

namespace Utils;
use \Exception;

class Emailer
{
	public $emailFromAddress;
	public $emailFromName;
	public $emailReplyToAddress;
	public $emailReplyToName;
	public $emailTo;
	public $emailSubject;
	public $emailMessage;

	// can accept comma-separated email address
	public function validateEmail(string $emailToCheck)
	{
		//iterate over emails in string
		if (stristr($emailToCheck, ',')) {
			$emails = explode(',', $emailToCheck);
		} else {
			$emails[] = $emailToCheck;
		}

		foreach ($emails as $email) {
			if(!filter_var(trim($email), FILTER_VALIDATE_EMAIL))
			{
				throw new Exception("Email address $email is not valid.");
			}
			else
			{
				return true;
			}
		}
	}

	protected function validateEmailSubject(string $subject)
	{
        if(! isset($subject) || $subject == '')
        {
            throw new Exception("Subject cannot be empty.");
        }
		else
		{
			return true;
		}
	}

	protected function validateEmailMessage(string $message)
	{
        if(! isset($message) || $message == '')
        {
            throw new Exception("Message cannot be empty.");
        }
		else
		{
			return true;
		}
	}

	protected function sanitizeInput(string $input)
	{
		$input = strip_tags(htmlentities($input, ENT_QUOTES, 'UTF-8'));
		return $input;
	}


	protected function createHeaders(string $emailFromName, $emailFromAddress, $emailReplyToAddress, $emailReplyToName)
	{
		$eol = "\r\n";
		$sent_date = date("r");
		# Common Headers
		$headers = "From: " . $emailFromName . "<" . $emailFromAddress . ">".$eol;
		$headers .= "Reply-To: " . $emailReplyToName . "<" . $emailReplyToAddress . ">".$eol;
		$headers .= "Return-Path: " . $emailFromName . "<" . $emailFromAddress . ">".$eol;    // these two to set reply address
		$headers .= "Message-ID: <".time()."-" . $emailFromAddress . ">".$eol;
		$headers .= "X-Mailer: PHP v".phpversion().$eol;          // These two to help avoid spam-filters
		$headers .= "Date: $sent_date\r\n";

		return $headers;
	}

	public function sendEmail()
	{

		if(
			$this->validateEmail($this->emailFromAddress)
			&& $this->validateEmail($this->emailReplyToAddress)
			&& $this->validateEmail($this->emailTo)
			&& $this->validateEmailSubject($this->emailSubject)
			&& $this->validateEmailMessage($this->emailMessage)
		)
		{
			$sanitizedSubject = $this->sanitizeInput($this->emailSubject);
			$sanitizedMessage = $this->sanitizeInput($this->emailMessage);

			$headers = $this->createHeaders($this->emailFromName,
				$this->emailFromAddress,
				$this->emailReplyToAddress,
				$this->emailReplyToName,
			);

			$result = mail($this->emailTo, $sanitizedSubject, $sanitizedMessage, $headers);

			//Return true if the mail was sent successfully.
			return $result;
		}
	}

}

?>