<div class="container-fluid  w-75 mx-auto mt-8">
    <form wire:submit.prevent='searchUser' action="">
        <div class="form-floating mb-3">
            <input type="text"  wire:model='name' class=" bg-dark text-white form-control" id="floatingInput" placeholder="name@example.com">
            <label for="floatingInput" class="text-white">
                <i class="bi bi-search"></i> Search for users...
            </label>
            <button type="submit"></button>
        </div>
    </form>
    <ul class="list-group " >
        @foreach ($users as $user)
            <li class="list-group-item bg-dark text-white mb-1 list-group-item-action"
                wire:click='checkconversation({{$user->id}})'>
                {{$user->name}}
            </li>
        @endforeach
    </ul>
</div>
<script>
    // Send request to search for a user whenever a new letter is entered
    $(function () {
        $("form").each(function () {
            $(this)
                .find("input")
                .keyup(function (e) {
                    $("button").click()
                });
        });
    });
</script>
