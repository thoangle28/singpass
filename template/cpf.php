 <!-- Button trigger modal -->
 <button class="btn btn-gray me-5" data-bs-toggle="modal" data-bs-target="#cpfModal">
     More information
 </button>

 <!-- Modal -->
 <div class="modal fade" id="cpfModal" tabindex="-1" aria-labelledby="cpfModal" aria-hidden="true">
     <div class="modal-dialog modal-dialog-centered modal-lg">
         <div class="modal-content">
             <div class="modal-header">
                 <h5 class="modal-title" id="exampleModalLabel">More Information From Singpass</h5>
                 <button class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
             </div>
             <div class="modal-body">
                 <!-- Nav tabs -->
                 <ul class="nav nav-tabs" id="myTab" role="tablist">
                     <li class="nav-item" role="presentation">
                         <a class="nav-link active" id="cpf-tab" data-bs-toggle="tab" href="#cpf" role="tab" aria-controls="cpf" aria-selected="true">CPF</a>
                     </li>
                     <li class="nav-item" role="presentation">
                         <a class="nav-link" id="vehicle-tab" data-bs-toggle="tab" href="#vehicle" role="tab" aria-controls="vehicle" aria-selected="false">Vehicle</a>
                     </li>
                 </ul>

                 <!-- Tab panes -->
                 <div class="tab-content" id="myTabContent">
                     <div class="tab-pane fade show active" id="cpf" role="tabpanel" aria-labelledby="cpf-tab">
                         <table class="table">
                             <thead>
                                 <tr>
                                     <th scope="col">#</th>
                                     <th scope="col">Date</th>
                                     <th scope="col">Employer</th>
                                     <th scope="col" class="text-right">Amount</th>
                                     <th scope="col">Month</th>
                                 </tr>
                             </thead>
                             <tbody>
                                 <?php
                                    if ($cpf && $cpf['date']) {
                                        foreach ($cpf['date'] as $id => $item) { ?>
                                         <tr>
                                             <th scope="col"><?php print $id + 1; ?></th>
                                             <td><?php print $item; ?></td>
                                             <td><?php print $cpf['employer'][$id]; ?></td>
                                             <td class="text-right"><?php print format_money($cpf['amount'][$id]); ?></td>
                                             <td><?php print $cpf['month'][$id]; ?></td>
                                         </tr>
                                     <?php }
                                    } else { ?>
                                     <tr>
                                         <td colspan="5" class="text-center p-3">
                                             No matching records found
                                         </td>
                                     </tr>
                                 <?php } ?>
                             </tbody>
                         </table>
                     </div>
                     <div class="tab-pane fade" id="vehicle" role="tabpanel" aria-labelledby="vehicle-tab">
                         <table class="table">
                             <thead>
                                 <tr>
                                     <th scope="col">#</th>
                                     <th scope="col">Date</th>
                                     <th scope="col">Employer</th>
                                     <th scope="col" class="text-right">Amount</th>
                                     <th scope="col">Month</th>
                                 </tr>
                             </thead>
                             <tbody>
                                 <tr>
                                     <td colspan="5" class="text-center p-3">
                                         No matching records found
                                     </td>
                                 </tr>
                             </tbody>
                         </table>
                     </div>
                 </div>
             </div>
         </div>
     </div>
 </div>