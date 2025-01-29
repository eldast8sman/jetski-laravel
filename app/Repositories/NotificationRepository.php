<?php

namespace App\Repositories;

use App\Models\Notification;
use App\Repositories\Interfaces\NotificationRepositoryInterface;
use App\Services\AuthService;

class NotificationRepository extends AbstractRepository implements NotificationRepositoryInterface
{
    private $guard;
    private $auth;

    public function __construct(Notification $notification, $guard='admin-api')
    {
        parent::__construct($notification);
        $this->guard = $guard;
        $this->auth = new AuthService($guard);
    }

    public function index($limit = 10)
    {
        $type = substr($this->guard, -4);
        $data = [
            ['type', $type],
            ['type_id', $this->auth->logged_in_user()->id]
        ];
        $orderBy = [
            ['created_at', 'desc']
        ];

        $notifications = $this->findBy($data, $orderBy, $limit);
        return $notifications;
    }

    public function store(array $data)
    {
        $data['read'] = 0;
        $data['type'] = substr($this->guard, -4);
        $notification = $this->create($data);
        return $notification;
    }
}