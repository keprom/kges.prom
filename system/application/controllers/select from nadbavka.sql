select  DISTINCT(firm.dogovor), 
		firm.name,
		nadbavka_absolutnaya.data,
		billing_point.name,
		counter_type.name,
		nadbavka_absolutnaya.value,
		nadbavka_absolutnaya.tariff_value
from industry.firm
left join industry.billing_point
on billing_point.firm_id = firm.id
left join industry.counter
on counter.point_id = billing_point.id
left join industry.values_set
on values_set.counter_id = counter.id
left join industry.counter_type
on counter.type_id = counter_type.id
left join industry.nadbavka_absolutnaya
on values_set.id = nadbavka_absolutnaya.values_set_id
where nadbavka_absolutnaya.data >='2018-01-05' and nadbavka_absolutnaya.data <='2018-01-31' and nadbavka_absolutnaya.tariff_value = 18.6700
order by firm.dogovor
