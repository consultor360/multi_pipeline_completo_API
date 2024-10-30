<?php
// Caminho: /public_html/modules/multi_pipeline/controllers/Api.php
defined('BASEPATH') or exit('No direct script access allowed');

// Carregar o REST_Controller e a classe Format manualmente
require_once __DIR__ . '/../libraries/REST_Controller.php';
require_once __DIR__ . '/../libraries/Format.php';

class Api extends REST_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Api_model');
        $this->load->model('Lead_model');
        $this->load->library('form_validation');
    }

    /**
     * Autenticação via token para acesso externo
     */
    private function authenticate_token()
    {
        $token = $this->input->get_request_header('Authorization');
        if (!$this->Api_model->is_valid_token($token)) {
            $this->response([
                'status' => 'error',
                'message' => 'Autenticação inválida',
                'code' => 403
            ], RESTController::HTTP_FORBIDDEN);
            return false;
        }
        return true;
    }

    /**
     * Endpoint para adicionar um novo lead
     * Método: POST
     */
    public function add_lead_post()
    {
        if (!$this->authenticate_token()) {
            return;
        }

        // Configuração de validação dos campos
        $required_fields = [
            'name' => 'Nome', 'email' => 'Email',
            'pipeline_id' => 'Pipeline ID', 'stage_id' => 'Stage ID',
            'status' => 'Status', 'source' => 'Fonte'
        ];

        foreach ($required_fields as $field => $label) {
            $this->form_validation->set_rules($field, $label, 'required|trim');
        }

        $optional_fields = [
            'title', 'company', 'description', 'country', 'zip',
            'city', 'state', 'address', 'assigned', 'phonenumber',
            'website', 'is_public', 'lead_value'
        ];

        foreach ($optional_fields as $field) {
            $this->form_validation->set_rules($field, ucfirst($field), 'trim');
        }

        if ($this->form_validation->run() == FALSE) {
            $this->response([
                'status' => 'error',
                'message' => validation_errors(),
                'code' => 400
            ], REST_Controller::HTTP_BAD_REQUEST);
            return;
        }

        // Preparar dados do lead
        $lead_data = [];
        foreach (array_merge(array_keys($required_fields), $optional_fields) as $field) {
            $value = $this->post($field);
            if ($value !== false) {
                $lead_data[$field] = $value;
            }
        }

        // Adicionar lead
        $result = $this->Lead_model->add_lead_api($lead_data);

        // Responder com status adequado
        $this->response([
            'status' => $result['success'] ? 'success' : 'error',
            'message' => $result['message'],
            'lead_id' => isset($result['lead_id']) ? $result['lead_id'] : null
        ], $result['code']);
    }

    /**
     * Endpoint para adicionar um token
     * Método: POST
     */
    public function add_token_post() // Alterado para add_token_post
    {
        if (!$this->authenticate_token()) {
            return;
        }

        // Captura os dados do formulário
        $data = $this->input->post();
        $data['user_id'] = get_staff_user_id();

        // Verifica se uma data de expiração foi fornecida
        if (empty($data['expires_at'])) {
            // Define uma expiração padrão de 30 dias se não especificado
            $data['expires_at'] = date('Y-m-d H:i:s', strtotime('+30 days'));
        }

        $this->form_validation->set_rules('name', 'Nome do Token', 'required|trim');

        if ($this->form_validation->run() == FALSE) {
            $this->response([
                'status' => 'error',
                'message' => validation_errors(),
                'code' => 400
            ], REST_Controller::HTTP_BAD_REQUEST);
        } else {
            $token_id = $this->Api_model->add_token($data);

            $this->response([
                'status' => $token_id ? 'success' : 'error',
                'message' => $token_id ? 'Token adicionado com sucesso' : 'Erro ao adicionar token',
                'token_id' => $token_id
            ], $token_id ? REST_Controller::HTTP_CREATED : REST_Controller::HTTP_BAD_REQUEST);
        }
    }

}