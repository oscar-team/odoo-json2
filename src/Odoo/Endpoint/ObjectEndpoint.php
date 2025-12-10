<?php

namespace OdooJson2\Odoo\Endpoint;

use OdooJson2\Odoo\Config;
use OdooJson2\Odoo\Context;
use OdooJson2\Odoo\Request\Arguments\Domain;
use OdooJson2\Odoo\Request\Arguments\Options;
use OdooJson2\Odoo\Request\CheckAccessRights;
use OdooJson2\Odoo\Request\Create;
use OdooJson2\Odoo\Request\FieldsGet;
use OdooJson2\Odoo\Request\Read;
use OdooJson2\Odoo\Request\ReadGroup;
use OdooJson2\Odoo\Request\Request;
use OdooJson2\Odoo\Request\RequestBuilder;
use OdooJson2\Odoo\Request\Search;
use OdooJson2\Odoo\Request\SearchRead;
use OdooJson2\Odoo\Request\Unlink;
use OdooJson2\Odoo\Request\Write;

class ObjectEndpoint extends Endpoint
{
    protected string $service = 'object';

    public function __construct(Config $config, protected Context $context, protected int $uid)
    {
        parent::__construct($config);
    }

    public function setContext(Context $context): void
    {
        $this->context = $context;
    }

    public function execute(Request $request, ?Options $options = null)
    {
        $options ??= new Options();

        $value = $request->execute(
            client: $this->getClient(),
            database: $this->getConfig()->getDatabase(),
            options: $options->withContext($this->context)
        );
        return $value;
    }

    public function model(string $model, ?Domain $domain = null): RequestBuilder
    {
        return new RequestBuilder(
            endpoint: $this,
            model: $model,
            domain: $domain ?? new Domain()
        );
    }

    public function checkAccessRights(string $model, string $permission, ?Options $options = null): bool
    {
        return $this->execute(new CheckAccessRights(
            model: $model,
            permission: $permission
        ), $options);
    }

    public function count(string $model, ?Domain $domain = null, int $offset = 0, ?int $limit = null, ?string $order = null, ?Options $options = null): int
    {
        return $this->execute(new \OdooJson2\Odoo\Request\SearchCount(
            model: $model,
            domain: $domain ?? new Domain()
        ), $options);
    }

    public function search(string $model, ?Domain $domain = null, int $offset = 0, ?int $limit = null, ?string $order = null, ?Options $options = null): array
    {
        return $this->execute(new Search(
            model: $model,
            domain: $domain ?? new Domain(),
            offset: $offset,
            limit: $limit,
            order: $order
        ), $options);
    }

    public function read(string $model, array $ids, array $fields = [], ?Options $options = null): array
    {
        return $this->execute(new Read(
            model: $model,
            ids: $ids,
            fields: $fields
        ), $options);
    }

    public function searchRead(string $model, ?Domain $domain = null, ?array $fields = null, int $offset = 0, ?int $limit = null, ?string $order = null, ?Options $options = null): array
    {
        return $this->execute(new SearchRead(
            model: $model,
            domain: $domain ?? new Domain(),
            fields: $fields,
            offset: $offset,
            limit: $limit,
            order: $order
        ), $options);
    }

    public function readGroup(string $model, array $groupBy, ?Domain $domain = null, ?array $fields = null, int $offset = 0, ?int $limit = null, ?string $order = null, ?Options $options = null): array
    {
        return $this->execute(new ReadGroup(
            model: $model,
            groupBy: $groupBy,
            domain: $domain,
            fields: $fields,
            offset: $offset,
            limit: $limit,
            order: $order
        ), $options);
    }

    public function fieldsGet(string $model, ?array $fields = null, ?array $attributes = null, ?Options $options = null): object
    {
        return (object) $this->execute(new FieldsGet(
            model: $model,
            fields: $fields,
            attributes: $attributes
        ), $options);
    }

    public function create(string $model, array $values, ?Options $options = null): bool|int|array
    {
        $result = $this->execute(new Create(
            model: $model,
            values: $values
        ), $options);
        
        // JSON-2 API returns array of IDs when using vals_list
        // If single record was created, return the first ID as int
        // If multiple records, return the array
        if (is_array($result) && !empty($result)) {
            // Check if this was a single record creation (based on input)
            $firstKey = array_key_first($values);
            if (is_string($firstKey)) {
                // Single record - return first ID as int
                return (int) ($result[0] ?? 0);
            }
            // Multiple records - return array of IDs
            return $result;
        }
        
        return $result;
    }

    public function unlink(string $model, array $ids, ?Options $options = null): bool
    {
        return $this->execute(new Unlink(
            model: $model,
            ids: $ids
        ), $options);
    }

    public function write(string $model, array $ids, array $values, ?Options $options = null): bool
    {
        return $this->execute(new Write(
            model: $model,
            ids: $ids,
            values: $values
        ), $options);
    }
}

