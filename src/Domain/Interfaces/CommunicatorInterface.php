<?php

namespace Gateway\Domain\Interfaces;

interface CommunicatorInterface
{
    /**
     * Inicia una conexión con el servicio o protocolo. Tambien se puede generar el Cliente Http
     *
     * @return bool
     */
    public function connect():bool;

    /**
     * Cierra la conexión con el servicio o protocolo.
     *
     * @return bool
     */
    public function disconnect():bool;

    /**
     * Envía un mensaje o solicitud. p.e Method Post
     *
     * @param array $data
     * @return mixed
     */
    public function send(array $data):mixed;

    /**
     * Recibe un mensaje o respuesta. p.e Method Get
     *
     * @return mixed
     */
    public function receive():mixed;
    
    /**
     * Suscríbete a un topic, cola, endpoint, etc.
     *
     * @param string $topic
     * @return mixed
     */
    public function subscribe(string $topic);

    /**
     * Cancela la suscripción a un topic, cola, endpoint, etc.
     *
     * @param string $topic
     * @return mixed
     */
    public function unsubscribe(string $topic);
    
}