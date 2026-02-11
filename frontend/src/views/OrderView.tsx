import { useState, useEffect } from 'react';
import { Container, Table, Badge, Button, Spinner, Alert } from 'react-bootstrap';
import { useNavigate } from 'react-router-dom';
import { Eye, ChevronLeft } from 'lucide-react';
import api from '../api/api';

interface OrderSummary {
    id: number;
    status: string;
    total: string;
    createdAt: string;
}

const OrderView = () => {
    const [orders, setOrders] = useState<OrderSummary[]>([]);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState('');
    const navigate = useNavigate();

    useEffect(() => {
        const fetchOrders = async () => {
            try {
                const response = await api.get('/api/orders');
                // Assuming the API returns an array or a wrapper
                setOrders(response.data.orders || response.data.data || []);
            } catch (err) {
                setError('Error al cargar la lista de pedidos');
            } finally {
                setLoading(false);
            }
        };
        fetchOrders();
    }, []);

    const getStatusBadge = (status: string) => {
        switch (status) {
            case 'completed': return <Badge bg="success">Completado</Badge>;
            case 'pending': return <Badge bg="warning" text="dark">Pendiente</Badge>;
            case 'processing': return <Badge bg="info">Procesando</Badge>;
            default: return <Badge bg="secondary">{status}</Badge>;
        }
    };

    if (loading) return <div className="text-center mt-5"><Spinner animation="border" /></div>;

    return (
        <Container fluid className="mt-5 px-4">
            <div className="d-flex justify-content-between align-items-center mb-4">
                <h2>Mis Pedidos</h2>
                <Button variant="outline-primary" onClick={() => navigate('/products')}>
                    <ChevronLeft size={18} /> Seguir Comprando
                </Button>
            </div>

            {error && <Alert variant="danger">{error}</Alert>}

            {orders.length === 0 ? (
                <Alert variant="info">Aún no has realizado ningún pedido.</Alert>
            ) : (
                <Table responsive hover className="bg-white rounded shadow-sm">
                    <thead className="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Fecha</th>
                            <th>Estado</th>
                            <th>Total</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        {orders.map((order) => (
                            <tr key={order.id}>
                                <td>#{order.id}</td>
                                <td>{new Date(order.createdAt).toLocaleDateString()}</td>
                                <td>{getStatusBadge(order.status)}</td>
                                <td className="fw-bold">${parseFloat(order.total).toFixed(2)}</td>
                                <td>
                                    <Button
                                        variant="link"
                                        size="sm"
                                        onClick={() => navigate(`/orders/${order.id}`)}
                                        className="p-0"
                                    >
                                        <Eye size={18} /> Ver detalles
                                    </Button>
                                </td>
                            </tr>
                        ))}
                    </tbody>
                </Table>
            )}
        </Container>
    );
};

export default OrderView;
