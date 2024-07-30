<?php

namespace Tempo\Attributes;


#[\Attribute]
class HttpMethod
{
    public function __construct(public string $method)
    {
    }
}


#[\Attribute]
class HttpRoute
{
    public string $route;

    public function __construct(string $route)
    {
        $this->route = $route;
    }
}



#[\Attribute]
class GetMethod
{
    public string $subroute;

    public function __construct(string $subroute = '')
    {
        $this->subroute = $subroute;
    }
}

#[\Attribute]
class PostMethod
{
    public string $subroute;

    public function __construct(string $subroute = '')
    {
        $this->subroute = $subroute;
    }
}

#[\Attribute]
class PutMethod
{
    public string $subroute;

    public function __construct(string $subroute = '')
    {
        $this->subroute = $subroute;
    }
}

#[\Attribute]
class DeleteMethod
{
    public string $subroute;

    public function __construct(string $subroute = '')
    {
        $this->subroute = $subroute;
    }
}

#[\Attribute]
class PatchMethod
{
    public string $subroute;

    public function __construct(string $subroute = '')
    {
        $this->subroute = $subroute;
    }
}