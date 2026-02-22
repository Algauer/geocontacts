<?php

namespace App\Http\Controllers;

use App\Http\Requests\Contact\StoreContactRequest;
use App\Http\Requests\Contact\UpdateContactRequest;
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

    public function index(Request $request): AnonymousResourceCollection
    {
        $contacts = $this->contactService->list(
            $request->user()->id,
            $request->query('search')
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
