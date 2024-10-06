    <style>
        table, th, tr, td {
            border: 1px solid black;
            border-collapse: collapse;
            table-layout: auto;
        }

        td {
            max-width: 600px;
        }
    </style>

<content class="content adminpage">
    <div class="container">
    <h1>Manage FAQ</h1>
        <div class="col">

            <div class="row">
                <div class="col">
                    <label for="category">Categories:</label>
                    <select class="form-control inv-dropdown" name="category" id="category">
                        <option selected value="null">All</option>
                        <?php for ($i = 0; $i < count($categories); $i++) { ?>
                            <option value="<?php echo $categories[$i]['id'] ?>"><?php echo $categories[$i]['category']?></option>
                        <?php } ?>
                    </select>

                    <label for="subcategories">Sub Categories:</label>
                    <select class="form-control inv-dropdown" name="subcategories" id="subcategories">
                        <option selected value="null">All</option>
                        <?php for ($i = 0; $i < count($subcategories); $i++) { ?>
                            <option value="<?php echo $subcategories[$i]['id'] ?>"><?php echo $subcategories[$i]['type']?></option>
                        <?php } ?>
                    </select>

                    <button class="btn" id="refresh">Refresh</button>
                    <button class="btn" id="addQuestion">Add</button>
                </div>
            </div>
            <br>
            <div class="row">
                <table id="questionTable">
                    <tr class="header">
                        <th>id</th>
                        <th>question</th>
                        <th>answer</th>
                        <th>category</th>
                        <th>subcategory</th>
                        <th>delete</th>
                        <th>edit</th>
                    </tr>

                    <?php for ($i = 0; $i < count($questions); $i++) { ?>
                        <tr>
                            <td><?php echo $questions[$i]['id']?></td>
                            <td><?php echo $questions[$i]['question']?></td>
                            <td><?php echo $questions[$i]['answer']?></td>
                            <td data-id="<?php echo $questions[$i]['fcid']?>"><?php echo (is_null($questions[$i]['category']) ? "null" : $questions[$i]['category']); ?></td>
                            <td data-id="<?php echo $questions[$i]['fscid']?>"><?php echo (is_null($questions[$i]['subCategory']) ? "null" : $questions[$i]['subCategory']); ?></td>
                            <td class="delete"><button class="deleteQuestion btn btnSmall">Delete</button></td>
                            <td class="edit"><button class="editQuestion btn btnSmall" value="<?php echo $questions[$i]['id']; ?>">Edit</button></td>
                        </tr>
                    <?php } ?>
                </table>
            </div>
        </div>

        <br>

        <div class="row">
            <div class="col">
                <label for="category">Categories:</label>

                <button class="btn btnSmall" id="addCategory">Add</button>

                <table id="categoryTable">
                    <tr class="header">
                        <th>id</th>
                        <th>category</th>
                        <th>delete</th>
                        <th>edit</th>
                    </tr>

                    <?php for ($i = 0; $i < count($categories); $i++) { ?>
                        <tr>
                            <td><?php echo $categories[$i]['id']?></td>
                            <td><?php echo $categories[$i]['category']?></td>
                            <td class="delete"><button class="deleteCategory btn btnSmall">Delete</button></td>
                            <td class="edit"><button  class="editCategory btn btnSmall" value="<?php echo $categories[$i]['id']; ?>">Edit</button></td>
                        </tr>
                    <?php } ?>
                </table>
            </div>

            <div class="col">
                <label for="subcategories">Sub Categories:</label>

                <button class="btn btnSmall" id="addSubCategory">Add</button>

                <table id="subCategoryTable">
                    <tr class="header">
                        <th>id</th>
                        <th>category</th>
                        <th>type</th>
                        <th>delete</th>
                        <th>edit</th>
                    </tr>
                    
                    <?php for ($i = 0; $i < count($subcategories); $i++) { ?>
                        <tr>
                            <td><?php echo $subcategories[$i]['id']?></td>
                            <td data-id="<?php echo $subcategories[$i]['catID']?>"><?php echo (is_null($subcategories[$i]['category']) ? "null" : $subcategories[$i]['category']); ?></td>
                            <td><?php echo $subcategories[$i]['type']?></td>
                            <td class="delete"><button class="deleteSubCategory btn btnSmall">Delete</button></td>
                            <td class="edit"><button  class="editSubCategory btn btnSmall" value="<?php echo $subcategories[$i]['id']; ?>">Edit</button></td>
                        </tr>
                    <?php } ?>
                </table>
            </div>
        </div>

    </div>

    <br><br>
</content>