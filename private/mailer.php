<?php
class mailer{
    var $Priority          = 3;
    var $CharSet           = "gb2312";
    var $ContentType        = "text/html";//plain
    var $Encoding          = "8bit";
    var $ErrorInfo         = "";
    var $From               = "sales@inouting.com";
    var $FromName           = "inouting";
    var $Sender            = "";
    var $Subject           = "";
    var $Body               = "";
    var $AltBody           = "";
    var $WordWrap          = 0;
    var $Mailer            = "smtp";
    var $Sendmail          = "/usr/sbin/sendmail";
    var $UseMSMailHeaders = false;
    var $PluginDir         = "";
    var $Version           = "1.54";
    var $ConfirmReadingTo  = "";
    var $LE           = "\n";
    // SMTP VARIABLES
    var $Host        = "mail.inouting.com";
    var $Port        = 25;
    var $Helo        = "inouting.com";
    var $SMTPAuth     = true;
    var $Username     = "sales@inouting.com";
    var $Password     = "nokia6230i";
    var $Timeout      = 10;
    var $SMTPDebug    = false;
    // PRIVATE VARIABLES
    var $to              = array();
    var $cc              = array();
    var $bcc             = array();
    var $ReplyTo         = array();
    var $attachment      = array();
    var $CustomHeader    = array();
    var $message_type    = "";
    var $boundary        = array();
    // VARIABLE METHODS
    function IsHTML($bool) {
        if($bool == true)
            $this->ContentType = "text/html";
        else
            $this->ContentType = "text/plain";
    }

		function IsSMTP() {
        $this->Mailer = "smtp";
    }

    function IsMail() {
        $this->Mailer = "mail";
    }

    function IsSendmail() {
        $this->Mailer = "sendmail";
    }

    function IsQmail() {
        //$this->Sendmail = "/var/qmail/bin/qmail-inject";
        $this->Sendmail = "/var/qmail/bin/sendmail";
        $this->Mailer = "sendmail";
    }

    function AddAddress($address, $name = "") {
        $cur = count($this->to);
        $this->to[$cur][0] = trim($address);
        $this->to[$cur][1] = $name;
    }

    function AddCC($address, $name = "") {
        $cur = count($this->cc);
        $this->cc[$cur][0] = trim($address);
        $this->cc[$cur][1] = $name;
    }

    function AddBCC($address, $name = "") {
        $cur = count($this->bcc);
        $this->bcc[$cur][0] = trim($address);
        $this->bcc[$cur][1] = $name;
    }

    function AddReplyTo($address, $name = "") {
        $cur = count($this->ReplyTo);
        $this->ReplyTo[$cur][0] = trim($address);
        $this->ReplyTo[$cur][1] = $name;
    }

    // MAIL SENDING METHODS
    function Send() {
        $header = "";
        $body = "";

        if((count($this->to) + count($this->cc) + count($this->bcc)) < 1)
        {
            $this->error_handler("You must provide at least one recipient email address");
            return false;
        }

        // Set whether the message is multipart/alternative
        if(!empty($this->AltBody))
            $this->ContentType = "multipart/alternative";

        // Attach sender information & date
        $header = $this->received();
        $header .= sprintf("Date: %s%s", $this->rfc_date(), $this->LE);
        $header .= $this->create_header();

        if(!$body = $this->create_body())
            return false;

        //echo "<pre>".$header . $body . "</pre>"; // debugging

        // Choose the mailer
        if($this->Mailer == "sendmail")
        {
          if(!$this->sendmail_send($header, $body))
              return false;
        }
        elseif($this->Mailer == "mail")
        {
          if(!$this->mail_send($header, $body))
              return false;
        }
        elseif($this->Mailer == "smtp")
        {
          if(!$this->smtp_send($header, $body))
              return false;
        }
        else
        {
            $this->error_handler(sprintf("%s mailer is not supported", $this->Mailer));
            return false;
        }

        return true;
    }

    function SendToQueue($queue_path, $send_time = 0) {
        $message = array();
        $header = "";
        $body = "";

        // If invalid or empty just set to the current time
        if($send_time == 0)
            $send_time = time();

        if(!is_dir($queue_path))
        {
            $this->error_handler("The supplied queue directory does not exist");
            return false;
        }

        if((count($this->to) + count($this->cc) + count($this->bcc)) < 1)
        {
            $this->error_handler("You must provide at least one recipient email address");
            return false;
        }

        // Set whether the message is multipart/alternative
        if(!empty($this->AltBody))
            $this->ContentType = "multipart/alternative";

        $header = $this->create_header();
        if(!$body = $this->create_body())
            return false;

        // Seed randomizer
        mt_srand(time());
        $msg_id = md5(uniqid(mt_rand()));

        $fp = fopen($queue_path . $msg_id . ".pqm", "wb");
        if(!$fp)
        {
            $this->error_handler(sprintf("Could not write to %s directory", $queue_path));
            return false;
        }

        $message[] = sprintf("----START PQM HEADER----%s", $this->LE);
        $message[] = sprintf("SendTime: %s%s", $send_time, $this->LE);
        $message[] = sprintf("Mailer: %s%s", $this->Mailer, $this->LE);

        // Choose the mailer
        if($this->Mailer == "sendmail")
        {
            $message[] = sprintf("Sendmail: %s%s", $this->Sendmail, $this->LE);
            $message[] = sprintf("Sender: %s%s", $this->Sender, $this->LE);
        }
        elseif($this->Mailer == "mail")
        {
            $message[] = sprintf("Sender: %s%s", $this->Sender, $this->LE);
            $message[] = sprintf("Subject: %s%s", $this->Subject, $this->LE);
            $message[] = sprintf("to: %s%s", $this->addr_list($this->to), $this->LE);
        }
        elseif($this->Mailer == "smtp")
        {
            $message[] = sprintf("Host: %s%s", $this->Host, $this->LE);
            $message[] = sprintf("Port: %d%s", $this->Port, $this->LE);
            $message[] = sprintf("Helo: %s%s", $this->Helo, $this->LE);
            $message[] = sprintf("Timeout: %d%s", $this->Timeout, $this->LE);

            if($this->SMTPAuth)
                $auth_no = 1;
            else
                $auth_no = 0;
            $message[] = sprintf("SMTPAuth: %d%s", $auth_no, $this->LE);
            $message[] = sprintf("Username: %s%s", $this->Username, $this->LE);
            $message[] = sprintf("Password: %s%s", $this->Password, $this->LE);
            $message[] = sprintf("From: %s%s", $this->From, $this->LE);

            $message[] = sprintf("to: %s%s", $this->addr_list($this->to), $this->LE);
            $message[] = sprintf("cc: %s%s", $this->addr_list($this->cc), $this->LE);
            $message[] = sprintf("bcc: %s%s", $this->addr_list($this->bcc), $this->LE);
        }
        else
        {
            $this->error_handler(sprintf("%s mailer is not supported", $this->Mailer));
            return false;
        }

        $message[] = sprintf("----END PQM HEADER----%s", $this->LE); // end of pqm header
        $message[] = $header;
        $message[] = $body;

        fwrite($fp, join("", $message));

        return ($msg_id . ".pqm");
    }

    function sendmail_send($header, $body) {
        if ($this->Sender != "")
            $sendmail = sprintf("%s -oi -f %s -t", $this->Sendmail, $this->Sender);
        else
            $sendmail = sprintf("%s -oi -t", $this->Sendmail);

        if(!@$mail = popen($sendmail, "w"))
        {
            $this->error_handler(sprintf("Could not execute %s", $this->Sendmail));
            return false;
        }

        fputs($mail, $header);
        fputs($mail, $body);

        $result = pclose($mail) >> 8 & 0xFF;
        if($result != 0)
        {
            $this->error_handler(sprintf("Could not execute %s", $this->Sendmail));
            return false;
        }

        return true;
    }

    function mail_send($header, $body) {
        //$to = substr($this->addr_append("To", $this->to), 4, -2);

        // Cannot add Bcc's to the $to
        $to = $this->to[0][0]; // no extra comma
        for($i = 1; $i < count($this->to); $i++)
            $to .= sprintf(",%s", $this->to[$i][0]);

        if ($this->Sender != "" && PHP_VERSION >= "4.0")
        {
            $old_from = ini_get("sendmail_from");
            ini_set("sendmail_from", $this->Sender);
        }

        if ($this->Sender != "" && PHP_VERSION >= "4.0.5")
        {
            // The fifth parameter to mail is only available in PHP >= 4.0.5
            $params = sprintf("-oi -f %s", $this->Sender);
            $rt = @mail($to, $this->Subject, $body, $header, $params);
        }
        else
        {
            $rt = @mail($to, $this->Subject, $body, $header);
        }

        if (isset($old_from))
            ini_set("sendmail_from", $old_from);

        if(!$rt)
        {
            $this->error_handler("Could not instantiate mail()");
            return false;
        }

        return true;
    }

    function smtp_send($header, $body) {
        // Include SMTP class code, but not twice
//        include_once($this->PluginDir . "class_smtp.php");

        $smtp = new SMTP;

        $smtp->do_debug = $this->SMTPDebug;

        // Try to connect to all SMTP servers
        $hosts = explode(";", $this->Host);
        $index = 0;
        $connection = false;
        $smtp_from = "";
        $bad_rcpt = array();
        $e = "";

        // Retry while there is no connection
        while($index < count($hosts) && $connection == false)
        {
            if(strstr($hosts[$index], ":"))
                list($host, $port) = explode(":", $hosts[$index]);
            else
            {
                $host = $hosts[$index];
                $port = $this->Port;
            }

            if($smtp->Connect($host, $port, $this->Timeout))
                $connection = true;
            //printf("%s host could not connect<br>", $hosts[$index]); //debug only
            $index++;
        }
        if(!$connection)
        {
            $this->error_handler("SMTP Error: could not connect to SMTP host server(s)");
            return false;
        }

        // Must perform HELO before authentication
        $smtp->Hello($this->Helo);

        // If user requests SMTP authentication
        if($this->SMTPAuth)
        {
            if(!$smtp->Authenticate($this->Username, $this->Password))
            {
                $this->error_handler("SMTP Error: Could not authenticate");
                return false;
            }
        }

        if ($this->Sender == "")
            $smtp_from = $this->From;
        else
            $smtp_from = $this->Sender;

        if(!$smtp->Mail(sprintf("<%s>", $smtp_from)))
        {
            $e = sprintf("SMTP Error: From address [%s] failed", $smtp_from);
            $this->error_handler($e);
            return false;
        }

        // Attempt to send attach all recipients
        for($i = 0; $i < count($this->to); $i++)
        {
            if(!$smtp->Recipient(sprintf("<%s>", $this->to[$i][0])))
                $bad_rcpt[] = $this->to[$i][0];
        }
        for($i = 0; $i < count($this->cc); $i++)
        {
            if(!$smtp->Recipient(sprintf("<%s>", $this->cc[$i][0])))
                $bad_rcpt[] = $this->cc[$i][0];
        }
        for($i = 0; $i < count($this->bcc); $i++)
        {
            if(!$smtp->Recipient(sprintf("<%s>", $this->bcc[$i][0])))
                $bad_rcpt[] = $this->bcc[$i][0];
        }

        // Create error message
        if(count($bad_rcpt) > 0)
        {
            for($i = 0; $i < count($bad_rcpt); $i++)
            {
                if($i != 0)
                    $e .= ", ";
                $e .= $bad_rcpt[$i];
            }
            $e = sprintf("SMTP Error: The following recipients failed [%s]", $e);
            $this->error_handler($e);

            return false;
        }


        if(!$smtp->Data(sprintf("%s%s", $header, $body)))
        {
            $this->error_handler("SMTP Error: Data not accepted");
            return false;
        }
        $smtp->Quit();

        return true;
    }

    // MESSAGE CREATION METHODS
    function addr_append($type, $addr) {
        $addr_str = $type . ": ";
        $addr_str .= $this->addr_format($addr[0]);
        if(count($addr) > 1)
        {
            for($i = 1; $i < count($addr); $i++)
            {
                $addr_str .= sprintf(", %s", $this->addr_format($addr[$i]));
            }
            $addr_str .= $this->LE;
        }
        else
            $addr_str .= $this->LE;

        return($addr_str);
    }

    function addr_list($list_array) {
        $addr_list = "";
        for($i = 0; $i < count($list_array); $i++)
        {
            if($i > 0)
                $addr_list .= ";";
            $addr_list .= $list_array[$i][0];
        }

        return $addr_list;
    }

    function addr_format($addr) {
        if(empty($addr[1]))
            $formatted = $addr[0];
        else
            $formatted = sprintf('"%s" <%s>', addslashes($addr[1]), $addr[0]);

        return $formatted;
    }

    function word_wrap($message, $length, $qp_mode = false) {
        if ($qp_mode)
        $soft_break = sprintf(" =%s", $this->LE);
        else
        $soft_break = $this->LE;

        $message = $this->fix_eol($message);
        if (substr($message, -1) == $this->LE)
        $message = substr($message, 0, -1);

        $line = explode($this->LE, $message);
        $message = "";
        for ($i=0 ;$i < count($line); $i++)
        {
          $line_part = explode(" ", $line[$i]);
          $buf = "";
          for ($e = 0; $e<count($line_part); $e++)
          {
              $word = $line_part[$e];
              if ($qp_mode and (strlen($word) > $length))
              {
                $space_left = $length - strlen($buf) - 1;
                if ($e != 0)
                {
                    if ($space_left > 20)
                    {
                        $len = $space_left;
                        if (substr($word, $len - 1, 1) == "=")
                          $len--;
                        elseif (substr($word, $len - 2, 1) == "=")
                          $len -= 2;
                        $part = substr($word, 0, $len);
                        $word = substr($word, $len);
                        $buf .= " " . $part;
                        $message .= $buf . sprintf("=%s", $this->LE);
                    }
                    else
                    {
                        $message .= $buf . $soft_break;
                    }
                    $buf = "";
                }
                while (strlen($word) > 0)
                {
                    $len = $length;
                    if (substr($word, $len - 1, 1) == "=")
                        $len--;
                    elseif (substr($word, $len - 2, 1) == "=")
                        $len -= 2;
                    $part = substr($word, 0, $len);
                    $word = substr($word, $len);

                    if (strlen($word) > 0)
                        $message .= $part . sprintf("=%s", $this->LE);
                    else
                        $buf = $part;
                }
              }
              else
              {
                $buf_o = $buf;
                if ($e == 0)
                    $buf .= $word;
                else
                    $buf .= " " . $word;
                if (strlen($buf) > $length and $buf_o != "")
                {
                    $message .= $buf_o . $soft_break;
                    $buf = $word;
                }
              }
          }
          $message .= $buf . $this->LE;
        }

        return ($message);
    }

    function create_header() {
        $header = array();

        // Set the boundaries
        $uniq_id = md5(uniqid(time()));
        $this->boundary[1] = "b1_" . $uniq_id;
        $this->boundary[2] = "b2_" . $uniq_id;

        // To be created automatically by mail()
        if(($this->Mailer != "mail") && (count($this->to) > 0))
            $header[] = $this->addr_append("To", $this->to);

        $header[] = sprintf("From: \"%s\" <%s>%s", addslashes($this->FromName),
                            trim($this->From), $this->LE);
        if(count($this->cc) > 0)
            $header[] = $this->addr_append("Cc", $this->cc);

        // sendmail and mail() extract Bcc from the header before sending
        if((($this->Mailer == "sendmail") || ($this->Mailer == "mail")) && (count($this->bcc) > 0))
            $header[] = $this->addr_append("Bcc", $this->bcc);

        if(count($this->ReplyTo) > 0)
            $header[] = $this->addr_append("Reply-to", $this->ReplyTo);

        // mail() sets the subject itself
        if($this->Mailer != "mail")
            $header[] = sprintf("Subject: %s%s", trim($this->Subject), $this->LE);

        $header[] = sprintf("X-Priority: %d%s", $this->Priority, $this->LE);
        $header[] = sprintf("X-Mailer: phpmailer [version %s]%s", $this->Version, $this->LE);
        $header[] = sprintf("Return-Path: %s%s", trim($this->From), $this->LE);

        if($this->ConfirmReadingTo != "")
            $header[] = sprintf("Disposition-Notification-To: <%s>%s",
                            trim($this->ConfirmReadingTo), $this->LE);

        // Add custom headers
        for($index = 0; $index < count($this->CustomHeader); $index++)
            $header[] = sprintf("%s%s", $this->CustomHeader[$index], $this->LE);

        if($this->UseMSMailHeaders)
            $header[] = $this->AddMSMailHeaders();

        $header[] = sprintf("MIME-Version: 1.0%s", $this->LE);

        // Determine what type of message this is
        if(count($this->attachment) < 1 && strlen($this->AltBody) < 1)
            $this->message_type = "plain";
        else
        {
            if(count($this->attachment) > 0)
                $this->message_type = "attachments";
            if(strlen($this->AltBody) > 0 && count($this->attachment) < 1)
                $this->message_type = "alt";
            if(strlen($this->AltBody) > 0 && count($this->attachment) > 0)
                $this->message_type = "alt_attachments";
        }

        switch($this->message_type)
        {
            case "plain":
                $header[] = sprintf("Content-Transfer-Encoding: %s%s",
                                    $this->Encoding, $this->LE);
                $header[] = sprintf("Content-Type: %s; charset = \"%s\"",
                                    $this->ContentType, $this->CharSet);
                break;
            case "attachments":
            case "alt_attachments":
                if($this->EmbeddedImageCount() > 0)
                {
                    $header[] = sprintf("Content-Type: %s;%s\ttype=\"text/html\";%s\tboundary=\"%s\"%s",
                                    "multipart/related", $this->LE, $this->LE,
                                    $this->boundary[1], $this->LE);
                }
                else
                {
                    $header[] = sprintf("Content-Type: %s;%s",
                                    "multipart/mixed", $this->LE);
                    $header[] = sprintf("\tboundary=\"%s\"%s", $this->boundary[1], $this->LE);
                }
                break;
            case "alt":
                $header[] = sprintf("Content-Type: %s;%s",
                                    "multipart/alternative", $this->LE);
                $header[] = sprintf("\tboundary=\"%s\"%s", $this->boundary[1], $this->LE);
                break;
        }

        // No additional lines when using mail() function
        if($this->Mailer != "mail")
            $header[] = $this->LE.$this->LE;

        return(join("", $header));
    }

    function create_body() {
        $body = array();

        // wordwrap the message body if set
        if($this->WordWrap > 0)
            $this->Body = $this->word_wrap($this->Body, $this->WordWrap);

        switch($this->message_type)
        {
            case "alt":
                // Return text of body
                $bndry = new Boundary($this->boundary[1]);
                $bndry->CharSet = $this->CharSet;
                $bndry->Encoding = $this->Encoding;
                $body[] = $bndry->GetSource();

                $body[] = sprintf("%s%s", $this->AltBody, $this->LE.$this->LE);

                $bndry = new Boundary($this->boundary[1]);
                $bndry->CharSet = $this->CharSet;
                $bndry->ContentType = "text/html";
                $bndry->Encoding = $this->Encoding;
                $body[] = $bndry->GetSource();

                $body[] = sprintf("%s%s", $this->Body, $this->LE.$this->LE);

                // End the boundary
                $body[] = sprintf("%s--%s--%s", $this->LE,
                                  $this->boundary[1], $this->LE.$this->LE);
                break;
            case "plain":
                $body[] = $this->Body;
                break;
            case "attachments":
                $bndry = new Boundary($this->boundary[1]);
                $bndry->CharSet = $this->CharSet;
                $bndry->ContentType = $this->ContentType;
                $bndry->Encoding = $this->Encoding;
                $body[] = sprintf("%s%s%s%s", $bndry->GetSource(false), $this->LE,
                                 $this->Body, $this->LE);

                if(!$body[] = $this->attach_all())
                    return false;
                break;
            case "alt_attachments":
                $body[] = sprintf("--%s%s", $this->boundary[1], $this->LE);
                $body[] = sprintf("Content-Type: %s;%s" .
                                  "\tboundary=\"%s\"%s",
                                   "multipart/alternative", $this->LE,
                                   $this->boundary[2], $this->LE.$this->LE);

                // Create text body
                $bndry = new Boundary($this->boundary[2]);
                $bndry->CharSet = $this->CharSet;
                $bndry->ContentType = "text/plain";
                $bndry->Encoding = $this->Encoding;
                $body[] = $bndry->GetSource() . $this->LE;

                $body[] = sprintf("%s%s", $this->AltBody, $this->LE.$this->LE);

                // Create the HTML body
                $bndry = new Boundary($this->boundary[2]);
                $bndry->CharSet = $this->CharSet;
                $bndry->ContentType = "text/html";
                $bndry->Encoding = $this->Encoding;
                $body[] = $bndry->GetSource() . $this->LE;

                $body[] = sprintf("%s%s", $this->Body, $this->LE.$this->LE);

                $body[] = sprintf("%s--%s--%s", $this->LE,
                                  $this->boundary[2], $this->LE.$this->LE);

                if(!$body[] = $this->attach_all())
                    return false;
                break;
        }
        // Add the encode string code here
        $sBody = join("", $body);
        $sBody = $this->encode_string($sBody, $this->Encoding);

        return $sBody;
    }

    // ATTACHMENT METHODS
    function AddAttachment($path, $name = "", $encoding = "base64", $type = "application/octet-stream") {
        if(!@is_file($path))
        {
            $this->error_handler(sprintf("Could not access [%s] file", $path));
            return false;
        }

        $filename = basename($path);
        if($name == "")
            $name = $filename;

        // Append to $attachment array
        $cur = count($this->attachment);
        $this->attachment[$cur][0] = $path;
        $this->attachment[$cur][1] = $filename;
        $this->attachment[$cur][2] = $name;
        $this->attachment[$cur][3] = $encoding;
        $this->attachment[$cur][4] = $type;
        $this->attachment[$cur][5] = false; // isStringAttachment
        $this->attachment[$cur][6] = "attachment";
        $this->attachment[$cur][7] = 0;

        return true;
    }

    function attach_all() {
        // Return text of body
        $mime = array();

        // Add all attachments
        for($i = 0; $i < count($this->attachment); $i++)
        {
            // Check for string attachment
            $isString = $this->attachment[$i][5];
            if ($isString)
            {
                $string = $this->attachment[$i][0];
            }
            else
            {
                $path = $this->attachment[$i][0];
            }
            $filename    = $this->attachment[$i][1];
            $name        = $this->attachment[$i][2];
            $encoding    = $this->attachment[$i][3];
            $type        = $this->attachment[$i][4];
            $disposition = $this->attachment[$i][6];
            $cid         = $this->attachment[$i][7];

            $mime[] = sprintf("--%s%s", $this->boundary[1], $this->LE);
            $mime[] = sprintf("Content-Type: %s; name=\"%s\"%s", $type, $name, $this->LE);
            $mime[] = sprintf("Content-Transfer-Encoding: %s%s", $encoding, $this->LE);

            if($disposition == "inline")
                $mime[] = sprintf("Content-ID: <%s>%s", $cid, $this->LE);
            else
                $mime[] = sprintf("Content-ID: <%s>%s", $name, $this->LE);

            $mime[] = sprintf("Content-Disposition: %s; filename=\"%s\"%s",
                              $disposition, $name, $this->LE.$this->LE);

            // Encode as string attachment
            if($isString)
            {
                if(!$mime[] = sprintf("%s%s", $this->encode_string($string, $encoding),
                                       $this->LE.$this->LE))
                  return false;
            }
            else
            {
                if(!$mime[] = sprintf("%s%s", $this->encode_file($path, $encoding),
                                      $this->LE.$this->LE))
                  return false;

            $mime[] = sprintf("--%s--%s", $this->boundary[1], $this->LE);

            }
        }

        return(join("", $mime));
    }

    function encode_file ($path, $encoding = "base64") {
        if(!@$fd = fopen($path, "rb"))
        {
            $this->error_handler(sprintf("File Error: Could not open file %s", $path));
            return false;
        }
        $file = fread($fd, filesize($path));
        $encoded = $this->encode_string($file, $encoding);
        fclose($fd);

        return($encoded);
    }

    function encode_string ($str, $encoding = "base64") {
        switch(strtolower($encoding)) {
          case "base64":
              // chunk_split is found in PHP >= 3.0.6
              $encoded = chunk_split(base64_encode($str));
              break;

          case "7bit":
          case "8bit":
              $encoded = $this->fix_eol($str);
              if (substr($encoded, -2) != $this->LE)
                $encoded .= $this->LE;
              break;

          case "binary":
              $encoded = $str;
              break;

          case "quoted-printable":
              $encoded = $this->encode_qp($str);
              break;

          default:
              $this->error_handler(sprintf("Unknown encoding: %s", $encoding));
              return false;
        }
        return($encoded);
    }

    function encode_qp ($str) {
        $encoded = $this->fix_eol($str);
        if (substr($encoded, -2) != $this->LE)
            $encoded .= $this->LE;

        // Replace every high ascii, control and = characters
        $encoded = preg_replace("/([\001-\010\013\014\016-\037\075\177-\377])/e",
                  "'='.sprintf('%02X', ord('\\1'))", $encoded);
        // Replace every spaces and tabs when it's the last character on a line
        $encoded = preg_replace("/([\011\040])".$this->LE."/e",
                  "'='.sprintf('%02X', ord('\\1')).'".$this->LE."'", $encoded);

        // Maximum line length of 76 characters before CRLF (74 + space + '=')
        $encoded = $this->word_wrap($encoded, 74, true);

        return $encoded;
    }

    function AddStringAttachment($string, $filename, $encoding = "base64", $type = "application/octet-stream") {
        // Append to $attachment array
        $cur = count($this->attachment);
        $this->attachment[$cur][0] = $string;
        $this->attachment[$cur][1] = $filename;
        $this->attachment[$cur][2] = $filename;
        $this->attachment[$cur][3] = $encoding;
        $this->attachment[$cur][4] = $type;
        $this->attachment[$cur][5] = true; // isString
        $this->attachment[$cur][6] = "attachment";
        $this->attachment[$cur][7] = 0;
    }

    function AddEmbeddedImage($path, $cid, $name = "", $encoding = "base64", $type = "application/octet-stream") {

        if(!@is_file($path))
        {
            $this->error_handler(sprintf("Could not access [%s] file", $path));
            return false;
        }

        $filename = basename($path);
        if($name == "")
            $name = $filename;

        // Append to $attachment array
        $cur = count($this->attachment);
        $this->attachment[$cur][0] = $path;
        $this->attachment[$cur][1] = $filename;
        $this->attachment[$cur][2] = $name;
        $this->attachment[$cur][3] = $encoding;
        $this->attachment[$cur][4] = $type;
        $this->attachment[$cur][5] = false; // isStringAttachment
        $this->attachment[$cur][6] = "inline";
        $this->attachment[$cur][7] = $cid;

        return true;
    }

    function EmbeddedImageCount() {
        $ret = 0;
        for($i = 0; $i < count($this->attachment); $i++)
        {
            if($this->attachment[$i][6] == "inline")
                $ret++;
        }

        return $ret;
    }

    // MESSAGE RESET METHODS
    function ClearAddresses() {
        $this->to = array();
    }

    function ClearCCs() {
        $this->cc = array();
    }

    function ClearBCCs() {
        $this->bcc = array();
    }

    function ClearReplyTos() {
        $this->ReplyTo = array();
    }

    function ClearAllRecipients() {
        $this->to = array();
        $this->cc = array();
        $this->bcc = array();
    }

    function ClearAttachments() {
        $this->attachment = array();
    }

    function ClearCustomHeaders() {
        $this->CustomHeader = array();
    }

    // MISCELLANEOUS METHODS
    function error_handler($msg) {
        $this->ErrorInfo = $msg;
    }

    function rfc_date() {
        $tz = date("Z");
        $tzs = ($tz < 0) ? "-" : "+";
        $tz = abs($tz);
        $tz = ($tz/3600)*100 + ($tz%3600)/60;
        $date = sprintf("%s %s%04d", date("D, j M Y H:i:s"), $tzs, $tz);
        return $date;
    }

    function received() {
        // Check for vars because they might not exist.  Possibly
        // write a small retrieval function (that mailer can use too!)

        $str = sprintf("Received: from phpmailer ([%s]) by %s " .
               "with HTTP;%s\t %s%s",
               $this->get_server_var("REMOTE_ADDR"),
               $this->get_server_var("SERVER_NAME"),
               $this->LE,
               $this->rfc_date(),
               $this->LE);

        return $str;
    }

    function get_server_var($varName) {
        global $HTTP_SERVER_VARS;
        global $HTTP_ENV_VARS;

        if(!isset($_SERVER))
        {
            $_SERVER = $HTTP_SERVER_VARS;
            if(!isset($_SERVER["REMOTE_ADDR"]))
                $_SERVER = $HTTP_ENV_VARS; // must be Apache
        }

        if(isset($_SERVER[$varName]))
            return $_SERVER[$varName];
        else
            return "";
    }

    function fix_eol($str) {
        $str = str_replace("\r\n", "\n", $str);
        $str = str_replace("\r", "\n", $str);
        $str = str_replace("\n", $this->LE, $str);
        return $str;
    }

    function AddCustomHeader($custom_header) {
        $this->CustomHeader[] = $custom_header;
    }

    function AddMSMailHeaders() {
        $MSHeader = "";
        if($this->Priority == 1)
            $MSPriority = "High";
        elseif($this->Priority == 5)
            $MSPriority = "Low";
        else
            $MSPriority = "Medium";

        $MSHeader .= sprintf("X-MSMail-Priority: %s%s", $MSPriority, $this->LE);
        $MSHeader .= sprintf("Importance: %s%s", $MSPriority, $this->LE);

        return($MSHeader);
    }

}


class Boundary{
    var $ID = 0;
    var $ContentType = "text/plain";
    var $Encoding = "";
    var $Disposition = "";
    var $FileName = "";
    var $CharSet = "";
    var $LE           = "\n";
    function Boundary($boundary_id) {
        $this->ID = $boundary_id;
    }

    function GetSource($bLineEnding = true) {
        $ret = array();
        $mime[] = sprintf("--%s%s", $this->ID, $this->LE);
        $mime[] = sprintf("Content-Type: %s; charset = \"%s\"%s",
                          $this->ContentType, $this->CharSet, $this->LE);
        if(strlen($this->Disposition) > 0)
        {
            $mime[] = sprintf("Content-Disposition: %s;");
            if(strlen($this->FileName) > 0)
                $mime[] = sprinf("filename=\"%s\"", $this->$this->FileName);
        }

        if($bLineEnding)
            $mime[] = $this->LE;

        return join("", $mime);
    }
}


class SMTP {
    var $SMTP_PORT = 25;
    var $CRLF = "\r\n";
    var $smtp_conn;
    var $error;
    var $helo_rply;
    var $do_debug;

    function SMTP() {
        $this->smtp_conn = 0;
        $this->error = null;
        $this->helo_rply = null;

        $this->do_debug = 0;
    }

    function Connect($host,$port=0,$tval=30) {
        $this->error = null;

        # make sure we are __not__ connected
        if($this->connected()) {
            $this->error =
                array("error" => "Already connected to a server");
            return false;
        }

        if(empty($port)) {
            $port = $this->SMTP_PORT;
        }

        #connect to the smtp server
        $this->smtp_conn = @fsockopen($host,    # the host of the server
                                     $port,    # the port to use
                                     $errno,   # error number if any
                                     $errstr,  # error message if any
                                     $tval);   # give up after ? secs
        # verify we connected properly
        if(empty($this->smtp_conn)) {
            $this->error = array("error" => "Failed to connect to server",
                                 "errno" => $errno,
                                 "errstr" => $errstr);
            if($this->do_debug >= 1) {
                echo "SMTP -> ERROR: " . $this->error["error"] .
                         ": $errstr ($errno)" . $this->CRLF;
            }
            return false;
        }

        if(substr(PHP_OS, 0, 3) != "WIN")
           socket_set_timeout($this->smtp_conn, 1, 0);

        # get any announcement stuff
        $announce = $this->get_lines();

        if($this->do_debug >= 2) {
            echo "SMTP -> FROM SERVER:" . $this->CRLF . $announce;
        }

        return true;
    }

    function Authenticate($username, $password) {
        // Start authentication
        fputs($this->smtp_conn,"AUTH LOGIN" . $this->CRLF);

        $rply = $this->get_lines();
        $code = substr($rply,0,3);

        if($code != 334) {
            $this->error =
                array("error" => "AUTH not accepted from server",
                      "smtp_code" => $code,
                      "smtp_msg" => substr($rply,4));
            if($this->do_debug >= 1) {
                echo "SMTP -> ERROR: " . $this->error["error"] .
                         ": " . $rply . $this->CRLF;
            }
            return false;
        }

        // Send encoded username
        fputs($this->smtp_conn, base64_encode($username) . $this->CRLF);

        $rply = $this->get_lines();
        $code = substr($rply,0,3);

        if($code != 334) {
            $this->error =
                array("error" => "Username not accepted from server",
                      "smtp_code" => $code,
                      "smtp_msg" => substr($rply,4));
            if($this->do_debug >= 1) {
                echo "SMTP -> ERROR: " . $this->error["error"] .
                         ": " . $rply . $this->CRLF;
            }
            return false;
        }

        // Send encoded password
        fputs($this->smtp_conn, base64_encode($password) . $this->CRLF);

        $rply = $this->get_lines();
        $code = substr($rply,0,3);

        if($code != 235) {
            $this->error =
                array("error" => "Password not accepted from server",
                      "smtp_code" => $code,
                      "smtp_msg" => substr($rply,4));
            if($this->do_debug >= 1) {
                echo "SMTP -> ERROR: " . $this->error["error"] .
                         ": " . $rply . $this->CRLF;
            }
            return false;
        }

        return true;
    }

    function Connected() {
        if(!empty($this->smtp_conn)) {
            $sock_status = socket_get_status($this->smtp_conn);
            if($sock_status["eof"]) {
                # hmm this is an odd situation... the socket is
                # valid but we aren't connected anymore
                if($this->do_debug >= 1) {
                    echo "SMTP -> NOTICE:" . $this->CRLF .
                         "EOF caught while checking if connected";
                }
                $this->Close();
                return false;
            }
            return true; # everything looks good
        }
        return false;
    }

    function Close() {
        $this->error = null; # so there is no confusion
        $this->helo_rply = null;
        if(!empty($this->smtp_conn)) {
            # close the connection and cleanup
            fclose($this->smtp_conn);
            $this->smtp_conn = 0;
        }
    }

    function Data($msg_data) {
        $this->error = null; # so no confusion is caused

        if(!$this->connected()) {
            $this->error = array(
                    "error" => "Called Data() without being connected");
            return false;
        }

        fputs($this->smtp_conn,"DATA" . $this->CRLF);

        $rply = $this->get_lines();
        $code = substr($rply,0,3);

        if($this->do_debug >= 2) {
            echo "SMTP -> FROM SERVER:" . $this->CRLF . $rply;
        }

        if($code != 354) {
            $this->error =
                array("error" => "DATA command not accepted from server",
                      "smtp_code" => $code,
                      "smtp_msg" => substr($rply,4));
            if($this->do_debug >= 1) {
                echo "SMTP -> ERROR: " . $this->error["error"] .
                         ": " . $rply . $this->CRLF;
            }
            return false;
        }

        $msg_data = str_replace("\r\n","\n",$msg_data);
        $msg_data = str_replace("\r","\n",$msg_data);
        $lines = explode("\n",$msg_data);

        $field = substr($lines[0],0,strpos($lines[0],":"));
        $in_headers = false;
        if(!empty($field) && !strstr($field," ")) {
            $in_headers = true;
        }

        $max_line_length = 998; # used below; set here for ease in change

        while(list(,$line) = @each($lines)) {
            $lines_out = null;
            if($line == "" && $in_headers) {
                $in_headers = false;
            }
            while(strlen($line) > $max_line_length) {
                $pos = strrpos(substr($line,0,$max_line_length)," ");
                $lines_out[] = substr($line,0,$pos);
                $line = substr($line,$pos + 1);
                if($in_headers) {
                    $line = "\t" . $line;
                }
            }
            $lines_out[] = $line;

            # now send the lines to the server
            while(list(,$line_out) = @each($lines_out)) {
                if(strlen($line_out) > 0)
                {
                    if(substr($line_out, 0, 1) == ".") {
                        $line_out = "." . $line_out;
                    }
                }
                fputs($this->smtp_conn,$line_out . $this->CRLF);
            }
        }

        fputs($this->smtp_conn, $this->CRLF . "." . $this->CRLF);

        $rply = $this->get_lines();
        $code = substr($rply,0,3);

        if($this->do_debug >= 2) {
            echo "SMTP -> FROM SERVER:" . $this->CRLF . $rply;
        }

        if($code != 250) {
            $this->error =
                array("error" => "DATA not accepted from server",
                      "smtp_code" => $code,
                      "smtp_msg" => substr($rply,4));
            if($this->do_debug >= 1) {
                echo "SMTP -> ERROR: " . $this->error["error"] .
                         ": " . $rply . $this->CRLF;
            }
            return false;
        }
        return true;
    }

    function Expand($name) {
        $this->error = null; # so no confusion is caused

        if(!$this->connected()) {
            $this->error = array(
                    "error" => "Called Expand() without being connected");
            return false;
        }

        fputs($this->smtp_conn,"EXPN " . $name . $this->CRLF);

        $rply = $this->get_lines();
        $code = substr($rply,0,3);

        if($this->do_debug >= 2) {
            echo "SMTP -> FROM SERVER:" . $this->CRLF . $rply;
        }

        if($code != 250) {
            $this->error =
                array("error" => "EXPN not accepted from server",
                      "smtp_code" => $code,
                      "smtp_msg" => substr($rply,4));
            if($this->do_debug >= 1) {
                echo "SMTP -> ERROR: " . $this->error["error"] .
                         ": " . $rply . $this->CRLF;
            }
            return false;
        }

        # parse the reply and place in our array to return to user
        $entries = explode($this->CRLF,$rply);
        while(list(,$l) = @each($entries)) {
            $list[] = substr($l,4);
        }

        return $rval;
    }

    function Hello($host="") {
        $this->error = null; # so no confusion is caused

        if(!$this->connected()) {
            $this->error = array(
                    "error" => "Called Hello() without being connected");
            return false;
        }

        if(empty($host)) {
            $host = "localhost";
        }

        fputs($this->smtp_conn,"HELO " . $host . $this->CRLF);

        $rply = $this->get_lines();
        $code = substr($rply,0,3);

        if($this->do_debug >= 2) {
            echo "SMTP -> FROM SERVER: " . $this->CRLF . $rply;
        }

        if($code != 250) {
            $this->error =
                array("error" => "HELO not accepted from server",
                      "smtp_code" => $code,
                      "smtp_msg" => substr($rply,4));
            if($this->do_debug >= 1) {
                echo "SMTP -> ERROR: " . $this->error["error"] .
                         ": " . $rply . $this->CRLF;
            }
            return false;
        }

        $this->helo_rply = $rply;

        return true;
    }

    function Help($keyword="") {
        $this->error = null; # to avoid confusion

        if(!$this->connected()) {
            $this->error = array(
                    "error" => "Called Help() without being connected");
            return false;
        }

        $extra = "";
        if(!empty($keyword)) {
            $extra = " " . $keyword;
        }

        fputs($this->smtp_conn,"HELP" . $extra . $this->CRLF);

        $rply = $this->get_lines();
        $code = substr($rply,0,3);

        if($this->do_debug >= 2) {
            echo "SMTP -> FROM SERVER:" . $this->CRLF . $rply;
        }

        if($code != 211 && $code != 214) {
            $this->error =
                array("error" => "HELP not accepted from server",
                      "smtp_code" => $code,
                      "smtp_msg" => substr($rply,4));
            if($this->do_debug >= 1) {
                echo "SMTP -> ERROR: " . $this->error["error"] .
                         ": " . $rply . $this->CRLF;
            }
            return false;
        }

        return $rply;
    }

    function Mail($from) {
        $this->error = null; # so no confusion is caused

        if(!$this->connected()) {
            $this->error = array(
                    "error" => "Called Mail() without being connected");
            return false;
        }

        fputs($this->smtp_conn,"MAIL FROM:" . $from . $this->CRLF);

        $rply = $this->get_lines();
        $code = substr($rply,0,3);

        if($this->do_debug >= 2) {
            echo "SMTP -> FROM SERVER:" . $this->CRLF . $rply;
        }

        if($code != 250) {
            $this->error =
                array("error" => "MAIL not accepted from server",
                      "smtp_code" => $code,
                      "smtp_msg" => substr($rply,4));
            if($this->do_debug >= 1) {
                echo "SMTP -> ERROR: " . $this->error["error"] .
                         ": " . $rply . $this->CRLF;
            }
            return false;
        }
        return true;
    }

    function Noop() {
        $this->error = null; # so no confusion is caused

        if(!$this->connected()) {
            $this->error = array(
                    "error" => "Called Noop() without being connected");
            return false;
        }

        fputs($this->smtp_conn,"NOOP" . $this->CRLF);

        $rply = $this->get_lines();
        $code = substr($rply,0,3);

        if($this->do_debug >= 2) {
            echo "SMTP -> FROM SERVER:" . $this->CRLF . $rply;
        }

        if($code != 250) {
            $this->error =
                array("error" => "NOOP not accepted from server",
                      "smtp_code" => $code,
                      "smtp_msg" => substr($rply,4));
            if($this->do_debug >= 1) {
                echo "SMTP -> ERROR: " . $this->error["error"] .
                         ": " . $rply . $this->CRLF;
            }
            return false;
        }
        return true;
    }

    function Quit($close_on_error=true) {
        $this->error = null; # so there is no confusion

        if(!$this->connected()) {
            $this->error = array(
                    "error" => "Called Quit() without being connected");
            return false;
        }

        # send the quit command to the server
        fputs($this->smtp_conn,"quit" . $this->CRLF);

        # get any good-bye messages
        $byemsg = $this->get_lines();

        if($this->do_debug >= 2) {
            echo "SMTP -> FROM SERVER:" . $this->CRLF . $byemsg;
        }

        $rval = true;
        $e = null;

        $code = substr($byemsg,0,3);
        if($code != 221) {
            # use e as a tmp var cause Close will overwrite $this->error
            $e = array("error" => "SMTP server rejected quit command",
                       "smtp_code" => $code,
                       "smtp_rply" => substr($byemsg,4));
            $rval = false;
            if($this->do_debug >= 1) {
                echo "SMTP -> ERROR: " . $e["error"] . ": " .
                         $byemsg . $this->CRLF;
            }
        }

        if(empty($e) || $close_on_error) {
            $this->Close();
        }

        return $rval;
    }

    function Recipient($to) {
        $this->error = null; # so no confusion is caused

        if(!$this->connected()) {
            $this->error = array(
                    "error" => "Called Recipient() without being connected");
            return false;
        }

        fputs($this->smtp_conn,"RCPT TO:" . $to . $this->CRLF);

        $rply = $this->get_lines();
        $code = substr($rply,0,3);

        if($this->do_debug >= 2) {
            echo "SMTP -> FROM SERVER:" . $this->CRLF . $rply;
        }

        if($code != 250 && $code != 251) {
            $this->error =
                array("error" => "RCPT not accepted from server",
                      "smtp_code" => $code,
                      "smtp_msg" => substr($rply,4));
            if($this->do_debug >= 1) {
                echo "SMTP -> ERROR: " . $this->error["error"] .
                         ": " . $rply . $this->CRLF;
            }
            return false;
        }
        return true;
    }

    function Reset() {
        $this->error = null; # so no confusion is caused

        if(!$this->connected()) {
            $this->error = array(
                    "error" => "Called Reset() without being connected");
            return false;
        }

        fputs($this->smtp_conn,"RSET" . $this->CRLF);

        $rply = $this->get_lines();
        $code = substr($rply,0,3);

        if($this->do_debug >= 2) {
            echo "SMTP -> FROM SERVER:" . $this->CRLF . $rply;
        }

        if($code != 250) {
            $this->error =
                array("error" => "RSET failed",
                      "smtp_code" => $code,
                      "smtp_msg" => substr($rply,4));
            if($this->do_debug >= 1) {
                echo "SMTP -> ERROR: " . $this->error["error"] .
                         ": " . $rply . $this->CRLF;
            }
            return false;
        }

        return true;
    }

    function Send($from) {
        $this->error = null; # so no confusion is caused

        if(!$this->connected()) {
            $this->error = array(
                    "error" => "Called Send() without being connected");
            return false;
        }

        fputs($this->smtp_conn,"SEND FROM:" . $from . $this->CRLF);

        $rply = $this->get_lines();
        $code = substr($rply,0,3);

        if($this->do_debug >= 2) {
            echo "SMTP -> FROM SERVER:" . $this->CRLF . $rply;
        }

        if($code != 250) {
            $this->error =
                array("error" => "SEND not accepted from server",
                      "smtp_code" => $code,
                      "smtp_msg" => substr($rply,4));
            if($this->do_debug >= 1) {
                echo "SMTP -> ERROR: " . $this->error["error"] .
                         ": " . $rply . $this->CRLF;
            }
            return false;
        }
        return true;
    }

    function SendAndMail($from) {
        $this->error = null; # so no confusion is caused

        if(!$this->connected()) {
            $this->error = array(
                "error" => "Called SendAndMail() without being connected");
            return false;
        }

        fputs($this->smtp_conn,"SAML FROM:" . $from . $this->CRLF);

        $rply = $this->get_lines();
        $code = substr($rply,0,3);

        if($this->do_debug >= 2) {
            echo "SMTP -> FROM SERVER:" . $this->CRLF . $rply;
        }

        if($code != 250) {
            $this->error =
                array("error" => "SAML not accepted from server",
                      "smtp_code" => $code,
                      "smtp_msg" => substr($rply,4));
            if($this->do_debug >= 1) {
                echo "SMTP -> ERROR: " . $this->error["error"] .
                         ": " . $rply . $this->CRLF;
            }
            return false;
        }
        return true;
    }

    function SendOrMail($from) {
        $this->error = null; # so no confusion is caused

        if(!$this->connected()) {
            $this->error = array(
                "error" => "Called SendOrMail() without being connected");
            return false;
        }

        fputs($this->smtp_conn,"SOML FROM:" . $from . $this->CRLF);

        $rply = $this->get_lines();
        $code = substr($rply,0,3);

        if($this->do_debug >= 2) {
            echo "SMTP -> FROM SERVER:" . $this->CRLF . $rply;
        }

        if($code != 250) {
            $this->error =
                array("error" => "SOML not accepted from server",
                      "smtp_code" => $code,
                      "smtp_msg" => substr($rply,4));
            if($this->do_debug >= 1) {
                echo "SMTP -> ERROR: " . $this->error["error"] .
                         ": " . $rply . $this->CRLF;
            }
            return false;
        }
        return true;
    }

    function Turn() {
        $this->error = array("error" => "This method, TURN, of the SMTP ".
                                        "is not implemented");
        if($this->do_debug >= 1) {
            echo "SMTP -> NOTICE: " . $this->error["error"] . $this->CRLF;
        }
        return false;
    }

    function Verify($name) {
        $this->error = null; # so no confusion is caused

        if(!$this->connected()) {
            $this->error = array(
                    "error" => "Called Verify() without being connected");
            return false;
        }

        fputs($this->smtp_conn,"VRFY " . $name . $this->CRLF);

        $rply = $this->get_lines();
        $code = substr($rply,0,3);

        if($this->do_debug >= 2) {
            echo "SMTP -> FROM SERVER:" . $this->CRLF . $rply;
        }

        if($code != 250 && $code != 251) {
            $this->error =
                array("error" => "VRFY failed on name '$name'",
                      "smtp_code" => $code,
                      "smtp_msg" => substr($rply,4));
            if($this->do_debug >= 1) {
                echo "SMTP -> ERROR: " . $this->error["error"] .
                         ": " . $rply . $this->CRLF;
            }
            return false;
        }
        return $rply;
    }

    function get_lines() {
        $data = "";
        while($str = fgets($this->smtp_conn,515)) {
            if($this->do_debug >= 4) {
                echo "SMTP -> get_lines(): \$data was \"$data\"" .
                         $this->CRLF;
                echo "SMTP -> get_lines(): \$str is \"$str\"" .
                         $this->CRLF;
            }
            $data .= $str;
            if($this->do_debug >= 4) {
                echo "SMTP -> get_lines(): \$data is \"$data\"" . $this->CRLF;
            }
            if(substr($str,3,1) == " ") { break; }
        }
        return $data;
    }

}
?>