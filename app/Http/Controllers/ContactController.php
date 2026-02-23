<?php

namespace App\Http\Controllers;

use App\Http\Requests\Contact\StoreContactRequest;
use App\Http\Requests\Contact\UpdateContactRequest;
use App\Http\Requests\Contact\ListContactRequest;
use App\Http\Resources\ContactResource;
use App\Services\ContactService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class ContactController extends Controller
{
    public function __construct(
        private ContactService $contactService
    ) {}

    public function index(ListContactRequest $request): AnonymousResourceCollection
    {
        $contacts = $this->contactService->list(
            $request->user()->id,
            $request->query('search'),
            $request->query('sort_by', 'name'),
            $request->query('sort_dir', 'asc'),
            (int) $request->query('per_page', 15)
        );

        return ContactResource::collection($contacts);
    }

    public function store(StoreContactRequest $request)
    {
        $contact = $this->contactService->create(
            $request->validated(),
            $request->user()->id
        );

        return (new ContactResource($contact))
            ->response()
            ->setStatusCode(201);
    }

    public function show(Request $request, string $contact): ContactResource
    {
        $found = $this->contactService->findForUser($contact, $request->user()->id);

        if (! $found) {
            abort(404);
        }

        return new ContactResource($found);
    }

    public function update(UpdateContactRequest $request, string $contact): ContactResource
    {
        $found = $this->contactService->findForUser($contact, $request->user()->id);

        if (! $found) {
            abort(404);
        }

        $updated = $this->contactService->update($found, $request->validated());

        return new ContactResource($updated);
    }

    public function destroy(Request $request, string $contact): Response
    {
        $found = $this->contactService->findForUser($contact, $request->user()->id);

        if (! $found) {
            abort(404);
        }

        $this->contactService->delete($found);

        return response()->noContent();
    }
}
