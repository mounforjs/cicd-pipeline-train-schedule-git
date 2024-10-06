<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Faq_model extends CI_Model {
    public function getCategories($id = null) {
        $this->db->select('id, type as category');
        $this->db->from('faq_category');

        if (null !== $id) {
            $this->db->where('id', $id);
        }

        $this->db->order_by('id', 'ASC');
        
        $result = $this->db->get()->result_array();

        return $result;
    }

    public function getSubCategories($id=null) {
        $this->db->select('fsc.id, fc.id as catID, fc.type as category, fsc.type');
        $this->db->from('faq_sub_category as fsc');
        $this->db->join('faq_category as fc', 'fsc.category = fc.id');
        if (isset($id)) {
            $this->db->where('category', $id);
        }

        $this->db->order_by('id', 'ASC');
        
        $result = $this->db->get()->result_array();

        return $result;
    }

    public function getAllQuestions($like = null) {
        $this->db->select('faq.id, question, answer, fc.id as fcid, fc.type as category, fsc.id as fscid, fsc.type as subCategory');
        $this->db->from('faq');
        $this->db->join('faq_category as fc', 'fc.id = faq.category', 'left');
        $this->db->join('faq_sub_category as fsc', 'fsc.id = faq.subCategory', 'left');
        if (isset($like) || $like === '') {
            $this->db->where('question LIKE', '%' . $like . '%');
        }

        $this->db->order_by('faq.id', 'ASC');

        $result = $this->db->get()->result_array();

        return $result;
    }

    public function getQuestions($categoryID=null, $subCategoryID=null) {
        $this->db->select('faq.id, question, answer, fc.id as fcid, fc.type as category, fsc.id as fscid, fsc.type as subCategory');
        $this->db->from('faq');
        $this->db->join('faq_category as fc', 'fc.id = faq.category', 'left');
        $this->db->join('faq_sub_category as fsc', 'fsc.id = faq.subCategory', 'left');

        if (isset($categoryID) && isset($subCategoryID)) {
            $this->db->where(array("fc.id" => $categoryID, "fsc.id" => $subCategoryID));
        } else if (isset($subCategoryID)) {
            $this->db->where('fsc.id', $subCategoryID);
        } else if (isset($categoryID)) {
            $this->db->where('fc.id', $categoryID);
        }

        $this->db->order_by('faq.id', 'ASC');

        $result = $this->db->get()->result_array();

        return $result;
    }

    public function getQuestion($id) {
        $this->db->select('faq.id, question, answer, fc.type as category, fsc.type as subCategory');
        $this->db->from('faq');
        $this->db->join('faq_category as fc', 'fc.id = faq.category', 'left');
        $this->db->join('faq_sub_category as fsc', 'fsc.id = faq.subCategory', 'left');
        $this->db->where('faq.id', $id);

        $result = $this->db->get()->result_array();

        return $result;
    }

    public function getFAQ($limit = null) {
        $result = $this->getCategories();
        
        $result = $this->appendQuestionsByCategory($result, $limit);
        
        return $result;
    }

    public function getSingleCateogoryFAQ($id, $noSCategory=false) {
        $result = $this->getCategories($id);
        if (count($result) == 0) {
            return;
        }
        
        if ($noSCategory) {
            $result = $this->appendQuestionsByCategory($result, null, array("subCategory" => null));
        } else {
            $result = $this->appendQuestionsBySubCategory($result);
        }
        
        
        return $result;
    }

    private function appendQuestionsByCategory($arr, $limit=null, $where=null) {
        foreach ($arr as $key => $value) {
            $this->db->select('id, question, subCategory');
            $this->db->from('faq');
            $this->db->where('category', $arr[$key]['id']);

            if (isset($where)) {
                $this->db->where($where);
            }

            if (isset($limit)) {
                if (is_int($limit)) {
                    $this->db->limit($limit);
                }
            } else {
                $this->db->limit(3);
            }

            $arr[$key]['faq'] = $this->db->get()->result_array();
        }

        return $arr;
    }

    private function appendQuestionsBySubCategory($arr) {
        foreach ($arr as $key => $value) {
            $subCategories = $this->getSubCategories($arr[$key]['id']);
            foreach ($subCategories as $key1 => $value1) {
                $this->db->select('id, question');
                $this->db->from('faq');
                $this->db->where('subCategory', $subCategories[$key1]['id']);

                $subCategories[$key1]['questions'] = $this->db->get()->result_array();
            }

            $arr[$key]['subCategories'] = $subCategories;
        }

        return $arr;
    }

    public function getCategoryID($category) {
        $this->db->select("id");
        $this->db->from("faq_category");
        $this->db->where("type", $category);

        return $this->db->get()->result_array()[0]["id"];
    }

    public function getSubCategoryID($subcategory) {
        $this->db->select("id");
        $this->db->from("faq_sub_category");
        $this->db->where("type", $subcategory);

        return $this->db->get()->result_array()[0]["id"];
    }

    public function addQuestion($id, $question, $answer, $category, $subcategory) {
        $catID = ($category == "null" || $category == "") ? NULL : $this->getCategoryID($category);
        $subCatID = ($subcategory == "null" || $subcategory == "") ? NULL : $this->getSubCategoryID($subcategory);

        $data = array("id" => $id, "question" => $question, "answer" => $answer, "category" => $catID, "subCategory" => $subCatID);

        $this->db->trans_start();
        $insert = $this->db->insert("faq", $data);
        $this->db->trans_complete();

        return $insert;
	}

	public function addCategory($id, $category) {
        $data = array("id" => $id, "type" => $category);

        $this->db->trans_start();
        $insert = $this->db->insert("faq_category", $data);
        $this->db->trans_complete();

        return $insert;
		
	}

	public function addSubCategory($id, $category, $subcategory) {
        $catID = $this->getCategoryID($category);
        
		$data = array("id" => $id, "category" => $catID, "type" => $subcategory);

        $this->db->trans_start();
        $insert = $this->db->insert("faq_sub_category", $data);
        $this->db->trans_complete();

        return $insert;
	}

	public function updateQuestion($oldID, $id, $question, $answer, $category, $subcategory) {
        $catID = ($category == "null" || $category == "") ? NULL : $this->getCategoryID($category);
        $subCatID = ($subcategory == "null" || $subcategory == "") ? NULL : $this->getSubCategoryID($subcategory);

        $data = array("id" => $id, "question" => $question, "answer" => $answer, "category" => $catID, "subCategory" => $subCatID);

        $this->db->trans_start();
        $this->db->where("id", $oldID);
        $update = $this->db->update("faq", $data);
        $this->db->trans_complete();

        return $update;
	}

	public function updateCategory($oldID, $id, $category) {
        $data = array("id" => $id, "type" => $category);
        
        $this->db->trans_start();
        $this->db->where("id", $oldID);
        $update = $this->db->update("faq_category", $data);
        $this->db->trans_complete();

        return $update;
	}

	public function updateSubCategory($oldID, $id, $category, $subcategory) {
        $catID = $this->getCategoryID($category);

        $data = array("id" => $id, "category" => $catID, "type" => $subcategory);
        
        $this->db->trans_start();
        $this->db->where("id", $oldID);
        $update = $this->db->update("faq_sub_category", $data);
        $this->db->trans_complete();

        return $update;
    }
    
    public function deleteQuestion($id) {
		$this->db->trans_start();
        $this->db->where("id", $id);
        $del = $this->db->delete("faq");
        $this->db->trans_complete();

        return $del;
	}

	public function deleteCategory($id) {
		$this->db->trans_start();
        $this->db->where("id", $id);
        $del = $this->db->delete("faq_category");
        $this->db->trans_complete();

        return $del;
	}

	public function deleteSubCategory($id) {
        $this->db->trans_start();
        $this->db->where("id", $id);
        $del = $this->db->delete("faq_sub_category");
        $this->db->trans_complete();

        return $del;
    }
    
    public function getIncrementedID($type) {
        $this->db->select("id");

        if ($type == "question") {
            $this->db->from("faq");
        } else if ($type == "category") {
            $this->db->from("faq_category");
        } else {
            $this->db->from("faq_sub_category");
        }
        
        $this->db->order_by("id", "DESC");
        $this->db->limit(1);

        $result = $this->db->get()->result_array()[0]["id"];

        return $result;
    }
}