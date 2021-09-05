<?php
	/**
	 * Created by Erwan.
	 * Date: 18/01/2018
	 * Time: 10:03
	 */

	class EmailCore
	{
		
		/**
		 * @var
		 */
		private $from;
		
		/**
		 * @var array
		 */
		private $to = [];
		
		/**
		 * @var array
		 */
		private $cc = [];
		
		/**
		 * @var array
		 */
		private $bcc = [];
		
		/**
		 * @var
		 */
		private $objet;
		
		/**
		 * @var
		 */
		private $file;
		
		/**
		 * @var
		 */
		private $message;
		
		/**
		 * @var
		 */
		private $send;
		
		
		/**
		 * @param $email
		 *
		 * @return $this
		 */
		public function getFrom ($email)
		{
			$this->from = $email;
			return $this;
		}
		
		/**
		 * @return $this
		 */
		public function getTo ()
		{
			$this->to = func_get_args();
			return $this;
		}
		
		/**
		 * @return $this
		 */
		public function getCc ()
		{
			$this->cc = func_get_args();
			return $this;
		}
		
		/**
		 * @return $this
		 */
		public function getBcc ()
		{
			$this->bcc = func_get_args();
			return $this;
		}
		
		/**
		 * @param $objet
		 *
		 * @return $this
		 */
		public function getObjet ($objet)
		{
			$this->objet = $objet;
			return $this;
		}
		
		/**
		 * @param $objet
		 *
		 * @return $this
		 */
		public function getFile ($link)
		{
			$this->file = $link;
			return $this;
		}
		
		/**
		 * @param $message
		 *
		 * @return $this
		 */
		public function getMessage ($message)
		{
			$this->message = $message;
			return $this;
		}
		
		/**
		 * Fonction envoie
		 */
		public function send ()
		{
			
			$to = implode(', ', $this->to);
			$cc = implode(', ', $this->cc);
			$bcc = implode(', ', $this->bcc);
			$l = "\r\n";
			
			if(!empty($this->file)){
				if(file_exists($this->file)){
					$file_name = $this->file;
					$typepiecejointe = filetype($file_name);
					$data = chunk_split( base64_encode(file_get_contents($file_name)));
					$file = basename($this->file);
					$boundary = md5(uniqid(time()));
				}
				else{
					$this->message = "Pièce jointe introuvable. <br>".$this->message;
				}
			}
			
			$entete[]  = "From: $this->from";
			$entete[] = "Reply-to: $this->from";
			
			if(!empty($this->bcc)) {
				$entete[] = "Bcc: $bcc";
				unset($this->bcc);
			}
			
			if(!empty($this->cc)) {
				$entete[] = "cc: $cc";
				unset($this->cc);
			}
			
			$entete[] = "X-Priority: 3";
			$entete[] = "MIME-Version: 1.0";
			
			if(!empty($this->file)){
				$entete[] = 'Content-Type: multipart/mixed;'.$l.' boundary="'.$boundary.'"'.$l.$l;
				
				$message = array(
					'--'.$boundary,
					'Content-Type: text/html; charset="utf-8"',
					'Content-Transfer-Encoding:8bit',
					$l.$this->message,
					'--'.$boundary,
					'Content-Type: "'.$typepiecejointe.'"; name="'.$file.'"',
					'Content-Transfer-Encoding: base64',
					'Content-Disposition: attachment; filename="'.$file.'"'.$l,
					$data.$l,
					$l."--".$boundary."--".$l
				);
			}
			else{
				$message = array($this->message);
			}
			
			$this->send = mail($to, $this->objet, implode($l, $message), implode($l, $entete));
			
			return $this;
		}
		
	}