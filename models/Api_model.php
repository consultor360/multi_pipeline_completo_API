<?php
// Caminho: /public_html/modules/multi_pipeline/models/Api_model.php
defined('BASEPATH') or exit('No direct script access allowed');

class Api_model extends CI_Model
{
    /**
     * Verifica se o token é válido e está ativo
     */
    public function is_valid_token($token)
    {
        $this->db->where('token', $token);
        $this->db->where('expires_at >=', date('Y-m-d H:i:s'));  // Verifica se o token não expirou
        $query = $this->db->get('tblmulti_pipeline_api_tokens');
    
        return $query->num_rows() > 0;
    }
    
    /**
     * Obtém todos os tokens, incluindo informações do usuário associado
     */
    public function get_all_tokens($include_user_info = true)
    {
        if ($include_user_info) {
            $this->db->select('tblmulti_pipeline_api_tokens.*, tblstaff.firstname, tblstaff.lastname');
            $this->db->join('tblstaff', 'tblstaff.staffid = tblmulti_pipeline_api_tokens.user_id', 'left');
        } else {
            $this->db->select('*');
        }
        $this->db->from('tblmulti_pipeline_api_tokens');
        return $this->db->get()->result();
    }

    /**
     * Adiciona um novo token de API
     */
public function add_token($data)
{
    $token_data = [
        'name'       => $data['name'],
        'token'      => app_generate_hash(),
        'user_id'    => $data['user_id'],
        'created_at' => date('Y-m-d H:i:s'),
        'expires_at' => $data['expires_at'] ?? null,  // Usando o valor fornecido ou `null` para não expirar
        'status'     => 1
    ];

    $this->db->insert('tblmulti_pipeline_api_tokens', $token_data);
    return $this->db->insert_id();
}


    /**
     * Salva um token com dados personalizados
     */
    public function save_token($token, $name)
    {
        $data = [
            'token'      => $token,
            'name'       => $name,
            'created_at' => date('Y-m-d H:i:s'),
            'expires_at' => date('Y-m-d H:i:s', strtotime('+30 days')),
            'status'     => 1
        ];
        $this->db->insert('tblmulti_pipeline_api_tokens', $data);
        return $this->db->affected_rows() > 0;
    }
}
