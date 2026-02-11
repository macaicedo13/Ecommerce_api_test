import { useState, useEffect } from 'react';
import { Container, Row, Col, Card, Badge, Table, Button, Alert, Spinner } from 'react-bootstrap';
import { useParams, useNavigate } from 'react-router-dom';
import { CheckCircle, CreditCard, ChevronLeft } from 'lucide-react';
import api from '../api/api';

interface OrderDetails {
    id: number;
    customerId: string;
    status: string;
    total: string;
    items: Array<{
        id: number;
        productName: string;
        quantity: number;
        unitPrice: string;
        subtotal: string;
    }>;
}

const OrderDetailsView = () => {
    const { id } = useParams<{ id: string }>();
    const [order, setOrder] = useState<OrderDetails | null>(null);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState('');
    const [processing, setProcessing] = useState(false);
    const navigate = useNavigate();

    const fetchOrder = async () => {
        try {
            const response = await api.get(`/api/orders/${id}`);
            setOrder(response.data.order || response.data);
        } catch (err) {
            setError('Error al cargar los detalles del pedido');
        } finally {
            setLoading(false);
        }
    };

    useEffect(() => {
        fetchOrder();
    }, [id]);

    const handleCheckout = async () => {
        setProcessing(true);
        try {
            const response = await api.post(`/api/orders/${id}/checkout`);
            if (response.status === 200) {
                await fetchOrder(); // Refresh to see updated status
            }
        } catch (err) {
            setError('Error al procesar el pago');
        } finally {
            setProcessing(false);
        }
    };

    if (loading) {
        return <div className="text-center mt-5"><Spinner animation="border" /></div>;
    }

    if (error || !order) {
        return (
            <Container fluid className="mt-5 px-4">
                <Alert variant="danger">{error || 'Pedido no encontrado'}</Alert>
                <Button onClick={() => navigate('/products')}><ChevronLeft /> Volver</Button>
            </Container>
        );
    }

    const getStatusBadge = (status: string) => {
        switch (status) {
            case 'completed': return <Badge bg="success">Completado</Badge>;
            case 'pending': return <Badge bg="warning" text="dark">Pendiente de Pago</Badge>;
            case 'processing': return <Badge bg="info">Procesando</Badge>;
            default: return <Badge bg="secondary">{status}</Badge>;
        }
    };

    return (
        <Container fluid className="mt-4 px-4">
            <div className="d-flex justify-content-between align-items-center mb-4">
                <h2>Detalles del Pedido #{order.id}</h2>
                {getStatusBadge(order.status)}
            </div>

            <Row>
                <Col lg={8}>
                    <Card className="shadow-sm border-0 mb-4">
                        <Card.Body>
                            <Table responsive>
                                <thead>
                                    <tr>
                                        <th>Producto</th>
                                        <th>Cantidad</th>
                                        <th>Precio</th>
                                        <th>Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {order.items.map((item) => (
                                        <tr key={item.id}>
                                            <td>{item.productName}</td>
                                            <td>{item.quantity}</td>
                                            <td>${parseFloat(item.unitPrice).toFixed(2)}</td>
                                            <td>${parseFloat(item.subtotal).toFixed(2)}</td>
                                        </tr>
                                    ))}
                                </tbody>
                            </Table>
                        </Card.Body>
                    </Card>
                </Col>
                <Col lg={4}>
                    <Card className="shadow-sm border-0">
                        <Card.Body>
                            <Card.Title>Estado del Pago</Card.Title>
                            <hr />
                            <div className="d-flex justify-content-between h4 mb-4">
                                <span>Total:</span>
                                <span className="text-primary">${parseFloat(order.total).toFixed(2)}</span>
                            </div>

                            {order.status === 'pending' && (
                                <Button
                                    variant="primary"
                                    className="w-100 py-2 d-flex align-items-center justify-content-center"
                                    onClick={handleCheckout}
                                    disabled={processing}
                                >
                                    <CreditCard className="me-2" />
                                    {processing ? 'Procesando...' : 'Pagar Ahora (Simulado)'}
                                </Button>
                            )}

                            {order.status === 'completed' && (
                                <div className="text-center text-success">
                                    <CheckCircle size={48} className="mb-2" />
                                    <h5>¡Pedido Pagado!</h5>
                                    <p className="small text-muted">Gracias por su compra.</p>
                                </div>
                            )}
                        </Card.Body>
                    </Card>
                </Col>
            </Row>
        </Container>
    );
};

export default OrderDetailsView;
