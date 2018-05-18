# How to implement a new checkList
## Required
1. make sure you have the following files:
  * app/Http/Controllers/CheckListController.php
  * app/Models/checkList.php
  * ressources/views/checkList/create.blade.php
  * ressources/views/checkList/show.blade.php
2. Create the routes to the CheckListController
  * Route::put('yourPath/id/{id}','CheckListController@update');
  * Route::get('yourPath/checkList/{id}/create','ProjectController@createcheckListItem');
  * Route::post('yourPath/checkList/{id}/create','CheckListController@store');
3. Create a new checkList where and when you want
  * checkList::newcheckList(tableName, elementId, checkListName);
4. To acces the checkList put it where you need
  * $yourVar = new checkList(tableName, elementId, checkListName);
5. On your view you can make a parent block that contains the checkList
6. To show each item, add the follow on the parent block
  * @each('checkList.show', $youVar->showToDo(), 'checkListItem')

## Exemple view
```php
<h1>{{$yourcheckList->getName()}}</h1>
<div class="yourcheckList">
  <div class="progressionLivrable">
    <div class="barre" style="background: linear-gradient(90deg, #20DE13 {{$yourcheckList->getCompletedPercent()}}%, #efefef 0%);"></div>
    <p>{{$yourcheckList->getNbItemsDone()}}/{{$yourcheckList->getNbItems()}}</p>
  </div>
    <ul>
        <!-- Display all yourcheckList -->
        @if($yourcheckList->showToDo())
          @each('checkList.show', $yourcheckList->showToDo(), 'checkListItem')
        @endif
    </ul>
    <ul class="completed hidden">
      @if($yourcheckList->showCompleted())
        @each('checkList.show', $yourcheckList->showCompleted(), 'checkListItem')
      @endif
    </ul>
    <a class="btn btn-warning addcheckList" data-id="{{$yourcheckList->getId()}}" data-URL="{{ URL('project') }}">Ajouter</a>
    @if($yourcheckList->getNbItemsDone())
      <a class="btn btn-warning changeView">Voir les éléments effectués</a>
      <a class="btn btn-warning changeView hidden">Cacher les éléments effectués</a>
    @endif
</div>
```

## Functions
### getName()
get the checkList Name

### getId()
get the checkList Id

### getNbItems()
get the number of items for the current checkList

### getNbItemsDone()
get the number of items completed for the current checkList

### getCompletedPercent()
get a number between 0 and 100 the represent how much percent the items are done

### showCompleted()
get items completed for the current checkList

### showToDo()
get items not completed for the current checkList

### static validate(itemId, true/false)
change status of item

### static newItem(checkListId, title, description)
add new item to the checkList

### static newcheckList(tableName, elementId, checkListName)
create a new checkList
