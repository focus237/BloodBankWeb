<?php

    require_once ROOT . 'Model/User/user.php';

    /**
     * User authentication and session manager
     */
    if (!class_exists('Auth'))
    {
        class Auth
        {
            
            /**
             * Determine if the user is connected or not
             * @return bool
             */
            public static function Connected()
            {
                if (!self::SessionStarted()) session_start();
                return isset($_SESSION['connected']);
            }

            /**
             * Create new session for the current user
             * @param string $username Username of the user to connect
             * @param string $phone Phone number of the user to connect
             */
            public static function Create($username, $phone)
            {
                session_start();
                session_regenerate_id(true);
                $_SESSION['username'] = $username;
                $_SESSION['phone'] = $phone;
                $_SESSION['connected'] = true;
            }

            /**
             * Disconnect user and close session
             */
            public static function Close()
            {
                if (self::Connected())
                {
                    session_unset();
                    session_destroy();
                }
            }

            /**
             * Determine if the session was started
             * @return bool
             */
            private static function SessionStarted()
            {
                return session_id() != null;
            }

            /**
             * Return the phone of the connected user
             * @return string
             */
            public static function GetPhone()
            {
                if (self::Connected())
                    return $_SESSION['phone'];
                return null;
            }

            /**
             * Return the username of the connected user
             * @return string
             */
            public static function GetUsername()
            {
                if (self::Connected())
                    return $_SESSION['username'];
                return null;
            }

            /**
             * Return the bloodgroup of the connected user
             * @return string
             */
            public static function GetBloodGroup()
            {
                if (self::Connected())
                {
                    return User::GetByPhone(self::GetPhone())->GetBloodGroup();
                }
                return null;
            }
        }
    }