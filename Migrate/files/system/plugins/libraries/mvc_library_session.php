<?php

class MVC_Library_Session
{
    public function has($key)
    {
        return (isset($_SESSION[$key]) ? true : false);
    }

    public function set($key, $value)
    {
        return $_SESSION[$key] = $value;
    }

    public function get($key)
    {
        if($this->has($key))
            return $_SESSION[$key];
        else
            return false;
    }

    public function delete($key)
    {
        unset($_SESSION[$key]);
    }

    public function destroy()
    {
        return session_destroy();
    }
}
